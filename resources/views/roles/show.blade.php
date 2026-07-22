@extends('layouts.app-shell')

@section('title', "{$role->name} Role")
@section('page-title', $role->name)
@section('page-subtitle', "{$role->users_count} users assigned")

@push('styles')
    <style>
        .role-show-page {
            display: grid;
            justify-items: start;
        }

        .role-show-panel {
            width: min(720px, 100%);
            padding: 24px;
            border: 1px solid #d8e2f2;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: var(--shadow);
        }

        .role-show-title {
            margin: 0 0 8px;
            color: #071b4d;
            font-size: 22px;
            line-height: 1.1;
            font-weight: 900;
        }

        .role-show-meta {
            margin: 0 0 18px;
            color: #61708f;
            font-size: 13px;
            font-weight: 700;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 22px;
        }

        .tag {
            min-height: 28px;
            display: inline-flex;
            align-items: center;
            padding: 0 10px;
            border: 1px solid #cbd9ee;
            border-radius: 999px;
            background: #f8fbff;
            color: #17315f;
            font-size: 12px;
            font-weight: 850;
        }

        .button {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            border-radius: 6px;
            background: #1766ff;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <div class="role-show-page">
        <section class="role-show-panel">
            <h2 class="role-show-title">{{ $role->name }}</h2>
            <p class="role-show-meta">{{ $role->users_count }} users assigned</p>

            <div class="tags">
                @forelse ($role->permissions as $permission)
                    <span class="tag">{{ $permission->name }}</span>
                @empty
                    <span class="tag">No permissions assigned</span>
                @endforelse
            </div>

            <a class="button" href="{{ route('roles.index', ['view' => $role->id]) }}">Back to Roles</a>
        </section>
    </div>
@endsection
