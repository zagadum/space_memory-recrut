<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<style>
    body {
        margin: 0;
    }

    *, *::before, *::after {
        box-sizing: border-box;
    }

    .scene {
        width: 100%;
        height: 100vh;
        position: relative;
        background: #01070a;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 30px;
    }

    h1 {
        font-family: "Days One", sans-serif;
        font-style: normal;
        line-height: 26px;
        font-weight: 400;
        font-size: 34px;
        color: #fff;
    }

    i {
        position: absolute;
        top: -250px;
        background: rgba(255, 255, 255, 0.5);
        animation: fall linear infinite;
    }

    @keyframes fall {
        from {
            transform: translateY(-100%); /* Начинаем выше верхней границы */
        }
        to {
            transform: translateY(200vh); /* Двигаемся вниз за пределы экрана */
        }
    }

    .rocket {
        position: relative;
        animation: animate 0.2s ease infinite;
    }

    .rocket:after, .rocket:before {
        content: '';
        width: 10px;
        height: 200px;
        position: absolute;
        bottom: -200px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(#00d0ff, transparent);
        border-radius: 20px 20px 10px 10px;
    }

    .rocket:after {
        filter: blur(20px);
    }

    @keyframes animate {
        0%, 100% {
            transform: translateY(-2px);
        }
        50% {
            transform: translateY(2px);
        }
    }
</style>
<body>
<div class="scene">
    <h1>
        Platform will be available soon
    </h1>
    <div class="rocket">
        <svg width="47" height="87" viewBox="0 0 47 87" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M23.9946 0.206672C23.7358 0.114196 23.4767 0.0402171 23.3103 0.206674C15.5609 4.84894 6.70181 24.5646 6.70181 40.045C6.70181 43.1521 6.96067 45.8155 7.39535 48.3031C4.72276 50.4578 2.57742 53.1211 1.45838 55.96C-1.806 64.2181 3.08599 72.5686 5.50888 75.8423C6.11001 76.6097 7.23806 76.7023 7.83002 75.9255L16.0047 65.6792C16.8647 66.5392 17.7249 67.2327 18.1502 67.6581C18.7514 68.0927 19.3523 68.3424 20.046 68.3518H27.2775C27.9619 68.3518 28.5722 68.0927 29.1733 67.6581C29.7743 67.2236 30.4587 66.5392 31.3187 65.6792L39.4935 75.9255C40.0947 76.693 41.2136 76.6097 41.8147 75.8423C44.2191 72.5686 49.1294 64.2181 45.865 55.96C44.6629 53.0378 42.5082 50.5317 39.9282 48.3031C40.3536 45.8062 40.6124 43.143 40.6218 40.045C40.6957 24.6387 32.0863 5.19109 23.9946 0.206672ZM23.6525 37.0395C19.8611 37.0395 16.7632 33.9416 16.7723 30.1595C16.7817 26.3772 19.8702 23.27 23.6525 23.2793C27.4348 23.2885 30.5419 26.3772 30.5327 30.1595C30.5327 33.9324 27.518 36.9471 23.6525 37.0395Z" fill="white" />
            <path d="M28.5538 70.164C28.5538 71.1998 28.0359 72.2355 27.2592 73.3452C25.5392 75.7496 21.9233 75.7589 20.194 73.3452C19.4266 72.2263 18.8254 71.1073 18.8161 70.2473C18.8161 69.7295 18.2151 69.4798 17.7896 69.9052C16.8464 71.1998 16.1528 72.7442 16.1528 74.4642C16.3378 78.4221 23.6525 86.2548 23.6525 86.2548C23.6525 86.2548 31.0506 78.5054 31.0506 74.381C31.0506 72.661 30.4495 71.0242 29.4138 69.8219C29.1641 69.3873 28.5538 69.6463 28.5538 70.164Z" fill="white" />
        </svg>
    </div>
</div>
<script>
    function stars() {
        const count = 30;
        const scene = document.querySelector('.scene');

        for (let i = 0; i < count; i++) {
            const star = document.createElement('i');
            const x = Math.random() * window.innerWidth;
            const duration = Math.random() * 1 + 0.5;
            const h = Math.random() * 100;

            star.style.left = x + 'px';
            star.style.width = '1px';
            star.style.height = (50 + h) + 'px';
            star.style.animationDuration = duration + 's';
            star.style.animationDelay = Math.random() * 1 + 's'; // Добавляем задержку анимации

            scene.appendChild(star);
        }
    }

    stars();
</script>
</body>
</html>