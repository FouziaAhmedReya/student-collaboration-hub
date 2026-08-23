/**
 * HubMap - Unified Leaflet + OpenFreeMap Helper
 * Provides display and location-picker capabilities with OpenStreetMap Nominatim search.
 */
window.HubMap = {
    // OpenFreeMap raster tile URL & options
    tileUrl: 'https://tiles.openfreemap.org/styles/bright/{z}/{x}/{y}.png',
    tileOptions: {
        maxZoom: 19,
        attribution: '&copy; <a href="https://openfreemap.org" target="_blank">OpenFreeMap</a> &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
    },

    /**
     * Initialize a display-only map.
     * @param {string|HTMLElement} containerId - Element ID or HTMLElement
     * @param {number} lat - Latitude
     * @param {number} lng - Longitude
     * @param {string} popupTitle - Popup text
     * @param {number} zoom - Default zoom level
     */
    initDisplayMap: function (containerId, lat, lng, popupTitle, zoom = 15) {
        const el = typeof containerId === 'string' ? document.getElementById(containerId) : containerId;
        if (!el || !lat || !lng || isNaN(lat) || isNaN(lng)) return null;

        try {
            const map = L.map(el, {
                zoomControl: true,
                scrollWheelZoom: false
            }).setView([lat, lng], zoom);

            L.tileLayer(this.tileUrl, this.tileOptions).addTo(map);

            const marker = L.marker([lat, lng]).addTo(map);
            if (popupTitle) {
                marker.bindPopup(`<strong>${popupTitle}</strong>`).openPopup();
            }

            // Invalidate size on load & resize to prevent grey tiles
            setTimeout(() => map.invalidateSize(), 300);
            return map;
        } catch (e) {
            console.error('HubMap initDisplayMap error:', e);
            return null;
        }
    },

    /**
     * Initialize an interactive location picker map.
     * @param {Object} config - Configuration options
     */
    initPickerMap: function (config) {
        const {
            containerId,
            latInputId,
            lngInputId,
            nameInputId,
            addressInputId,
            initialLat = 23.7806,
            initialLng = 90.4068,
            initialZoom = 14,
            onLocationSelect = null
        } = config;

        const el = document.getElementById(containerId);
        if (!el) return null;

        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);
        const nameInput = nameInputId ? document.getElementById(nameInputId) : null;
        const addressInput = addressInputId ? document.getElementById(addressInputId) : null;

        let curLat = latInput && latInput.value ? parseFloat(latInput.value) : initialLat;
        let curLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : initialLng;

        if (isNaN(curLat) || isNaN(curLng)) {
            curLat = initialLat;
            curLng = initialLng;
        }

        try {
            const map = L.map(el).setView([curLat, curLng], initialZoom);
            L.tileLayer(this.tileUrl, this.tileOptions).addTo(map);

            const marker = L.marker([curLat, curLng], { draggable: true }).addTo(map);

            function updateInputs(lat, lng) {
                const roundedLat = parseFloat(lat.toFixed(6));
                const roundedLng = parseFloat(lng.toFixed(6));

                if (latInput) latInput.value = roundedLat;
                if (lngInput) lngInput.value = roundedLng;

                marker.setLatLng([roundedLat, roundedLng]);

                if (typeof onLocationSelect === 'function') {
                    onLocationSelect(roundedLat, roundedLng);
                }
            }

            // Click anywhere on the map to set location
            map.on('click', function (e) {
                updateInputs(e.latlng.lat, e.latlng.lng);
            });

            // Drag marker to set location
            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                updateInputs(pos.lat, pos.lng);
            });

            // Manual coordinate inputs change listener
            if (latInput) {
                latInput.addEventListener('input', function () {
                    const l = parseFloat(latInput.value);
                    if (!isNaN(l) && marker) {
                        const cur = marker.getLatLng();
                        marker.setLatLng([l, cur.lng]);
                        map.panTo([l, cur.lng]);
                    }
                });
            }

            if (lngInput) {
                lngInput.addEventListener('input', function () {
                    const ln = parseFloat(lngInput.value);
                    if (!isNaN(ln) && marker) {
                        const cur = marker.getLatLng();
                        marker.setLatLng([cur.lat, ln]);
                        map.panTo([cur.lat, ln]);
                    }
                });
            }

            setTimeout(() => map.invalidateSize(), 300);

            return {
                map: map,
                marker: marker,
                setLocation: function (lat, lng, zoom = 15) {
                    map.setView([lat, lng], zoom);
                    updateInputs(lat, lng);
                }
            };
        } catch (e) {
            console.error('HubMap initPickerMap error:', e);
            return null;
        }
    },

    /**
     * Search address or place using OpenStreetMap Nominatim with debounce.
     * @param {string} query - Search term
     * @param {Function} callback - Callback with results array
     */
    searchNominatim: function (query, callback) {
        if (!query || query.trim().length < 3) {
            if (typeof callback === 'function') callback([]);
            return;
        }

        const endpoint = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`;

        fetch(endpoint, {
            headers: {
                'Accept': 'application/json',
                'User-Agent': 'StudentCollaborationHub-AcademicProject/1.0'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (typeof callback === 'function') {
                callback(data || []);
            }
        })
        .catch(err => {
            console.warn('Nominatim geocoding error:', err);
            if (typeof callback === 'function') callback([]);
        });
    }
};
