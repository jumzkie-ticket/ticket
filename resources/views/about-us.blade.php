@extends('layouts.app-shell')

@section('title', 'About Us')
@section('page-title', 'About Us')
@section('page-subtitle', 'Company, system, and release information.')

@push('styles')
    <style>
        .about-page {
            display: grid;
            gap: 18px;
        }

        .about-hero,
        .about-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .about-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(280px, .75fr);
            gap: 22px;
            align-items: stretch;
            overflow: hidden;
        }

        .about-copy {
            display: grid;
            align-content: center;
            gap: 18px;
            padding: 28px;
        }

        .about-kicker {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            width: fit-content;
            min-height: 28px;
            padding: 0 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .about-kicker svg {
            width: 14px;
            height: 14px;
        }

        .about-title {
            margin: 0;
            color: var(--ink);
            font-size: 30px;
            line-height: 1.08;
            font-weight: 900;
        }

        .about-summary {
            max-width: 760px;
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
            line-height: 1.6;
            text-align: justify;
        }

        .about-summary + .about-summary {
            margin-top: 10px;
        }

        .about-identity {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .identity-item {
            min-width: 0;
            padding: 13px 0 0;
            border-top: 1px solid var(--line);
        }

        .identity-label {
            display: block;
            margin-bottom: 7px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .identity-value {
            display: block;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .brand-preview {
            display: grid;
            place-items: center;
            min-height: 320px;
            padding: 28px;
            border-left: 1px solid var(--line);
            background:
                linear-gradient(135deg, rgba(var(--blue-rgb), .16), transparent 44%),
                color-mix(in srgb, var(--panel) 88%, var(--blue-soft));
        }

        .brand-frame {
            width: min(360px, 100%);
            aspect-ratio: 1.4;
            display: grid;
            place-items: center;
            padding: 26px;
            border: 1px solid rgba(var(--blue-rgb), .24);
            border-radius: 8px;
            background: var(--panel);
        }

        .brand-logo {
            width: 100%;
            max-height: 170px;
            object-fit: contain;
        }

        .brand-mark {
            color: var(--blue);
            font-size: 80px;
            line-height: 1;
            font-weight: 900;
        }

        .brand-mark sup {
            top: -1em;
            font-size: 30px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .about-card {
            min-height: 124px;
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            padding: 18px;
        }

        .about-card-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .about-card-icon svg {
            width: 19px;
            height: 19px;
        }

        .about-card-title {
            margin: 0 0 6px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .about-card-value {
            margin: 0;
            color: var(--ink);
            font-size: 18px;
            line-height: 1.2;
            font-weight: 900;
        }

        .release-details-card {
            grid-template-columns: 1fr;
            align-content: center;
            gap: 12px;
            padding: 14px;
        }

        .release-details-title {
            margin: 0;
            color: var(--ink);
            font-size: 15px;
            line-height: 1.2;
            font-weight: 900;
        }

        .maintainer-value {
            margin: 0;
            color: var(--ink);
            font-family: inherit;
            font-size: 15px;
            line-height: 1.3;
            font-weight: 900;
            letter-spacing: 0;
            overflow-wrap: normal;
            word-break: normal;
        }

        @media (max-width: 1180px) {
            .about-hero,
            .about-grid {
                grid-template-columns: 1fr;
            }

            .brand-preview {
                border-left: 0;
                border-top: 1px solid var(--line);
            }
        }

        @media (max-width: 640px) {
            .about-copy {
                padding: 16px;
            }

            .about-identity {
                grid-template-columns: 1fr;
            }

            .about-title {
                font-size: 24px;
            }

            .brand-preview {
                min-height: 220px;
                padding: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="about-page">
        <article class="about-hero">
            <div class="about-copy">
                <span class="about-kicker"><svg><use href="#icon-info"></use></svg><span>About</span></span>
                <div>
                    <h2 class="about-title">{{ $settings->company_name }}</h2>
                    <p class="about-summary">Xceler8 Technologies Inc. is a technology solutions provider dedicated to delivering innovative, reliable, and efficient software solutions that empower businesses to streamline operations and enhance customer satisfaction.</p>
                    <p class="about-summary">Through continuous innovation and a commitment to excellence, we aim to accelerate digital transformation and create lasting value for our clients and partners.</p>
                </div>
                <div class="about-identity">
                    <div class="identity-item">
                        <span class="identity-label">Company Name</span>
                        <span class="identity-value">{{ $settings->company_name }}</span>
                    </div>
                    <div class="identity-item">
                        <span class="identity-label">System Name</span>
                        <span class="identity-value">{{ $settings->system_name }}</span>
                    </div>
                </div>
            </div>

            <div class="brand-preview" aria-label="System brand">
                <div class="brand-frame">
                    @if ($logoUrl)
                        <img class="brand-logo" src="{{ $logoUrl }}" alt="{{ $settings->system_name }} logo">
                    @else
                        <span class="brand-mark">X<sup>8</sup></span>
                    @endif
                </div>
            </div>
        </article>

        <section class="about-grid" aria-label="System summary">
            <article class="about-card">
                <span class="about-card-icon"><svg><use href="#icon-info"></use></svg></span>
                <div>
                    <p class="about-card-title">Version</p>
                    <p class="about-card-value">{{ $versionInfo['version'] }}</p>
                </div>
            </article>
            <article class="about-card">
                <span class="about-card-icon"><svg><use href="#icon-calendar"></use></svg></span>
                <div>
                    <p class="about-card-title">First Release</p>
                    <p class="about-card-value">{{ $versionInfo['release_date'] }}</p>
                </div>
            </article>
            <article class="about-card release-details-card">
                <h3 class="release-details-title">Maintained By</h3>
                <p class="maintainer-value">{{ $maintainerName }}</p>
            </article>
        </section>

    </section>
@endsection
