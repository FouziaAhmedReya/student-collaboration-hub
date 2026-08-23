@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Back Link -->
    <div class="mb-3">
        <a href="{{ route('groups.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Study Groups
        </a>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header Title & Subtitle + Invite Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h2 fw-bold text-dark mb-0">Group Members</h1>
                <span class="badge bg-light text-primary border rounded-pill">{{ $group->name }}</span>
            </div>
            <p class="text-secondary mb-0 fs-6">Manage and collaborate with members ({{ $counts['active'] }} / {{ $group->max_members }} active members).</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-hub-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
                <i class="bi bi-person-plus-fill"></i> Invite Members
            </button>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="hub-card p-0 shadow-sm overflow-hidden">
        <!-- Tabs & Search Header -->
        <div class="p-3 border-bottom bg-light-subtle d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <!-- Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-1.5 px-3 small fw-semibold {{ $tab === 'all' ? 'active bg-primary' : 'text-secondary bg-white border' }}"
                       href="{{ route('groups.members.index', array_merge(['group' => $group->id], request()->except('tab', 'page'), ['tab' => 'all'])) }}">
                        All Members <span class="badge {{ $tab === 'all' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['all'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-1.5 px-3 small fw-semibold {{ $tab === 'admins' ? 'active bg-primary' : 'text-secondary bg-white border' }}"
                       href="{{ route('groups.members.index', array_merge(['group' => $group->id], request()->except('tab', 'page'), ['tab' => 'admins'])) }}">
                        Admins <span class="badge {{ $tab === 'admins' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['admins'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-1.5 px-3 small fw-semibold {{ $tab === 'pending' ? 'active bg-primary' : 'text-secondary bg-white border' }}"
                       href="{{ route('groups.members.index', array_merge(['group' => $group->id], request()->except('tab', 'page'), ['tab' => 'pending'])) }}">
                        Pending <span class="badge {{ $tab === 'pending' ? 'bg-white text-primary' : 'bg-warning-subtle text-dark border' }} ms-1 rounded-pill">{{ $counts['pending'] }}</span>
                    </a>
                </li>
            </ul>

            <!-- Search Form -->
            <form method="GET" action="{{ route('groups.members.index', $group) }}" class="d-flex gap-2" style="max-width: 320px; width: 100%;">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search members..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('groups.members.index', ['group' => $group->id, 'tab' => $tab]) }}" class="btn btn-outline-secondary border-start-0">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
            </form>
        </div>

        <!-- Members Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary text-uppercase small" style="font-size: 0.78rem;">
                    <tr>
                        <th class="ps-4 py-3">Member</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Joined On</th>
                        <th class="py-3">Status</th>
                        <th class="text-end pe-4 py-3">Actions / Activate</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($members->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="bi bi-people fs-2 d-block text-muted mb-2"></i>
                                No group members found in this view.
                            </td>
                        </tr>
                    @else
                        @foreach($members as $membership)
                            @php
                                $u = $membership->user;
                                $isGroupCreator = ((int)$membership->user_id === (int)$group->creator_id);
                            @endphp
                            <tr>
                                <!-- Member Profile & Info -->
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($u->profile && $u->profile->profile_photo)
                                            <img src="{{ asset('storage/' . $u->profile->profile_photo) }}" alt="{{ $u->name }}" class="rounded-circle border" width="42" height="42" style="object-fit: cover;">
                                        @else
                                            <div class="bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center border" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                {{ $u->name }}
                                                @if($isGroupCreator)
                                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill" style="font-size: 0.7rem;">Creator</span>
                                                @endif
                                            </div>
                                            <div class="text-secondary small">{{ $u->email }}</div>
                                            @if($u->profile && $u->profile->department)
                                                <div class="text-muted" style="font-size: 0.75rem;">Dept: {{ $u->profile->department }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Badge -->
                                <td class="py-3">
                                    @if($membership->role === 'admin')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-person me-1"></i> Member
                                        </span>
                                    @endif
                                </td>

                                <!-- Joined Date -->
                                <td class="py-3 text-secondary small">
                                    @if($membership->joined_at)
                                        {{ $membership->joined_at->format('M d, Y') }}
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $membership->joined_at->diffForHumans() }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Pending Approval</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3">
                                    @if($membership->status === 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-clock-history me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        @if($membership->status === 'pending')
                                            <!-- Activate / Approve Button -->
                                            <form method="POST" action="{{ route('groups.members.updateStatus', ['group' => $group->id, 'member' => $membership->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="btn btn-sm btn-success px-3" title="Approve and activate this member">
                                                    <i class="bi bi-check-lg me-1"></i> Activate
                                                </button>
                                            </form>

                                            <!-- Decline / Reject Button -->
                                            <form method="POST" action="{{ route('groups.members.updateStatus', ['group' => $group->id, 'member' => $membership->id]) }}" onsubmit="return confirm('Decline this join request?');">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Decline request">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Active Member Management -->
                                            @if(!$isGroupCreator)
                                                <!-- Role Toggle -->
                                                <form method="POST" action="{{ route('groups.members.updateRole', ['group' => $group->id, 'member' => $membership->id]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if($membership->role === 'admin')
                                                        <input type="hidden" name="role" value="member">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Demote to Member" onclick="return confirm('Demote this admin to regular member?');">
                                                            <i class="bi bi-arrow-down-circle me-1"></i> Demote
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="role" value="admin">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Promote to Admin" onclick="return confirm('Promote this member to group admin?');">
                                                            <i class="bi bi-arrow-up-circle me-1"></i> Make Admin
                                                        </button>
                                                    @endif
                                                </form>

                                                <!-- Remove Member -->
                                                <form method="POST" action="{{ route('groups.members.destroy', ['group' => $group->id, 'member' => $membership->id]) }}" onsubmit="return confirm('Remove {{ $u->name }} from the study group?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Member">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-light text-muted border px-2.5 py-1.5">Primary Owner</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
<div class="modal fade" id="inviteMemberModal" tabindex="-1" aria-labelledby="inviteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('groups.members.invite', $group) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="inviteMemberModalLabel">
                    <i class="bi bi-person-plus-fill text-primary me-2"></i> Invite Students to Group
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary small mb-3">
                    Select a registered student from the system to invite them to <strong class="text-dark">{{ $group->name }}</strong>.
                </p>

                @if($invitableUsers->isEmpty())
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> All existing registered students are already members or have pending invitations to this group.
                    </div>
                @else
                    <div class="mb-3">
                        <label for="invite_user_id" class="form-label fw-semibold text-dark">Select Student</label>
                        <select name="user_id" id="invite_user_id" class="form-select" required>
                            <option value="">-- Choose a student to invite --</option>
                            @foreach($invitableUsers as $invitable)
                                <option value="{{ $invitable->id }}">
                                    {{ $invitable->name }} ({{ $invitable->email }}) {{ $invitable->profile && $invitable->profile->department ? '• ' . $invitable->profile->department : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                @if(!$invitableUsers->isEmpty())
                    <button type="submit" class="btn btn-hub-primary">
                        <i class="bi bi-send-fill me-1"></i> Send Invitation
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
