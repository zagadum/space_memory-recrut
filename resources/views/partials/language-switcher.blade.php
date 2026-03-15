<div class="lang-switcher">
    @php
        $currentLocale = (string) app()->getLocale();
        $locales = [
            'pl' => 'PL',
            'en' => 'EN',
            'uk' => 'UA',
            'ru' => 'RU',
        ];
        $currentLabel = $locales[$currentLocale] ?? strtoupper($currentLocale);
    @endphp

    <div class="dropdown">
        <button class="btn btn-lang dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="lang-label">{{ $currentLabel }}</span>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            @foreach($locales as $code => $label)
                @if($code !== $currentLocale)
                    <a class="dropdown-item" href="{{ route('locale.switch', ['locale' => $code]) }}">
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
    .lang-switcher .dropdown-toggle::after {
        display: none; /* Hide default arrow for cleaner look */
    }
    
    .btn-lang {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.6);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 44px;
        text-align: center;
    }
    
    .btn-lang:hover, .btn-lang:focus, .show > .btn-lang {
        background: rgba(38, 249, 255, 0.06);
        color: #26F9FF;
        border-color: rgba(38, 249, 255, 0.3);
        box-shadow: 0 0 12px rgba(38, 249, 255, 0.1);
        outline: none;
    }

    .dropdown-menu {
        background: #0d2535;
        border: 1px solid rgba(38, 249, 255, 0.15);
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        margin-top: 6px;
        min-width: 60px;
        padding: 4px 0;
        overflow: hidden;
    }

    .dropdown-item {
        color: rgba(255, 255, 255, 0.5);
        font-size: 11px;
        font-weight: 700;
        padding: 6px 12px;
        text-align: center;
        transition: all 0.15s;
    }

    .dropdown-item:hover {
        background: rgba(38, 249, 255, 0.08);
        color: #26F9FF;
    }

    /* Integration Specifics */
    .father-nav .lang-switcher {
        margin-left: 15px;
        display: flex;
        align-items: center;
    }
    
    .sidebar-desktop .lang-switcher {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    
    #mobile-menu-overlay .lang-switcher {
        display: inline-block;
    }
</style>
