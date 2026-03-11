@section('styles')
    <style>
    /* === САЛЮТ === */
    #fireworks {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9998;
    }
    .firework {
        position: absolute;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #00f2ff;
        box-shadow: 0 0 8px #00f2ff;
        animation: explode 900ms ease-out forwards;
    }
    @keyframes explode {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: translate(var(--x), var(--y)) scale(0.2); opacity: 0; }
    }

    /* === ДЕД МОРОЗ И САНИ === */
    .ny-wrapper {
        position: fixed;
        left: 0;
        bottom: 40px;
        width: 100%;
        pointer-events: none;
        z-index: 9999;
    }
    .santa-scene {
        position: absolute;
        left: -400px;
        bottom: 0;
        display: flex;
        align-items: flex-end;
        gap: 40px;
    }
    .santa {
        width: 160px;
        height: 160px;
        position: absolute;
        margin-top:50pt;
        padding-left: 30pt;
    }
    .santa2 {
        width: 100px;
        height: 150px;
        position: absolute;
        margin-top:50pt;
        margin-left: -35pt;
    }
    .rope {
        width: 80px;
        height: 4px;
        background: #00f2ff;
        box-shadow: 0 0 8px rgba(0, 242, 255, 0.7);
        margin-bottom: 35px;
    }
    .sleigh {
        width: 280px;
        height: 80px;
        border-radius: 16px;
        border: 2px solid #00f2ff;
        box-shadow: 0 0 15px rgba(0, 242, 255, 0.6);
        background: rgba(0, 15, 30, 0.85);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #00f2ff;
        font-family: "Orbitron", sans-serif;
        text-transform: uppercase;
        position: relative;
    }
    .sleigh::after {
        content: "";
        position: absolute;
        bottom: -8px;
        left: 10px;
        right: 10px;
        height: 4px;
        border-radius: 4px;
        background: #00f2ff;
    }
    .logo { font-size: 14px; letter-spacing: 0.15em; margin-top: 5pt; }
    .year { font-size: 15px; font-weight: bold; margin-top: 2px; }
    </style>
@endsection

<div id="fireworks"></div>

<!-- Дед Мороз и сани -->
<div class="ny-wrapper">
    <div class="santa-scene">

        <div class="rope"></div>
        <img src="/images/ny/santa.png" alt="Дед Мороз" class="santa"  >
        <img src="/images/ny/snegurka.png" alt="Дед Мороз" class="santa2"  >
        <div class="sleigh">
            <span class="logo">SPACE-MEMORY</span>
            <span class="year">2026</span>
            <span class="year">{{ trans('student.index.new_year') }}</span>
        </div>
    </div>
</div>

@section('bottom-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        /* === АНИМАЦИЯ ДЕДА МОРОЗА === */
        $(function () {
            function runSanta() {
                var $scene = $('.santa-scene');
                var screenWidth = $(window).width();

                $scene.css({ left: -400 });

                $scene.animate(
                    { left: screenWidth },
                    15000,
                    'linear',
                    function () {
                        finish();
                        //setTimeout(runSanta, 6000);
                    }
                );
            }
            runSanta();
        });
        function finish() {

            $('.ny-wrapper').remove();
        }

        /* === САЛЮТ === */
        $(function () {
            function fireworkBoom() {
                const count = 40 + Math.floor(Math.random() * 20);
                const x = Math.random() * window.innerWidth;
                const y = Math.random() * window.innerHeight * 0.6;

                for (let i = 0; i < count; i++) {
                    const spark = $('<div class="firework"></div>');
                    const angle = (Math.PI * 2 / count) * i;
                    const distance = 80 + Math.random() * 60;

                    const dx = Math.cos(angle) * distance + "px";
                    const dy = Math.sin(angle) * distance + "px";

                    spark.css({
                        left: x + 'px',
                        top: y + 'px',
                        '--x': dx,
                        '--y': dy
                    });

                    $('#fireworks').append(spark);

                    setTimeout(() => spark.remove(), 1000);
                }
            }

            function randomFireworks() {
                fireworkBoom();
                setTimeout(randomFireworks, 1500 + Math.random() * 1000);
            }

            randomFireworks();
        });
    </script>
@endsection
