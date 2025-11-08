<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
       
    </head>
    <body>
        <div class="divBody">
            <div class="divBody2">
                <div class="divLeft">
                    <div class="divSample">
                        <div>
                            <div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="divRight">
                    <h1>Instagram</h1>
                    <h2>
                        <p>
                            athif_kyuziera
                        </p>
                    </h2>
                </div>
            </div>  
        </div>
        <div class="divError">
            <div class="divError2">
                <h1>@yield('message') @yield('code')</h1>
                <p>
                    Sedang ada perubahan atau perbaikan pada website, mohon tunggu sebentar ya, adik-adik!
                    <br>
                    Hubungi saya, Wildan Athif Muttaqien:
                </p>
                <div>
                    <a href="https://www.facebook.com/share/19SofgsQFs/" target="_blank">
                        <img src="/images/icons/facebook.png" />
                    </a>
                    <a href="https://www.instagram.com/athif_kyuziera/profilecard/?igsh=NHFsazN2a2diM3Rp" target="_blank">
                        <img src="/images/icons/instagram.png" />
                    </a>
                    <a href="https://wa.me/628985655826" target="_blank">
                        <img src="/images/icons/whatsapp.png" />
                    </a>
                    <a href="https://x.com/WildanAthif12" target="_blank">
                        <img src="/images/icons/x.png" />
                    </a>
                    <a href="https://www.linkedin.com/in/wildan-athif-muttaqien-89b327297" target="_blank">
                        <img src="/images/icons/linkedin.png" />
                    </a>
                </div>
            </div>
        </div>
        <div class="divBg"></div>

        <style>
            * {
                background: #f6ffa9;
                margin: 0;
            }
            .divBg {
                z-index: 10;
                position: fixed;
                background: #00000080;
                backdrop-filter: blur(8px);
                width: 100%;
                height: 100vh;
                animation: animasiError 1.2s ease-in;
            }
            .divError {
                z-index: 30;
                position: fixed;
                background: none;
                width: 100%;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: animasiError 1.2s ease-in;
            }
            .divError2 {
                padding: 20px;
                transform: translateY(-108px);
            }
            .divError div h1 {
                font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
                background: none;
                color: white;
                margin-bottom: 3px;
                animation: textError 2s infinite alternate;
            }
            .divError div p {
                font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
                background: none;
                color: white;
                line-height: 1.5;
                margin-bottom: 5px;
            }
            .divError div div {
                display: flex;
                justify-content: left;
                align-items: flex-start;
                gap: 10px;
            }
            .divError div div a {
                background: none;
                text-decoration: none;
            }
            .divError div div a img {
                width: 2.25rem;
                background: none;
            }
            @keyframes textError {
                50% {
                    color: red;
                    text-shadow: 2px 2px 5px red;
                }
            }

            @media screen and (min-width: 748px) {
                .divError div h1 {
                    font-size: 3rem;
                }
            }

            @media screen and (max-width: 747px) {
                .divError div h1 {
                    font-size: 2rem;
                }
                .divBody2 {
                    transform: translateY(-20px) scale(80%);
                }
                .divRight h2 p {
                    font-size: 20px; 
                }
            }

            @keyframes animasiError {
            0% {
                transform: translateY(-100vh);
                animation-timing-function: ease-in;
            }
            40% {
                transform: translateY(10vh) scaleY(120%);
                animation-timing-function: ease-out;
            }
            60% {
                transform: translateY(-5vh);
                animation-timing-function: ease-in-out;
            }
            75% {
                transform: translateY(2vh) scaleY(104%);
                animation-timing-function: ease-out;
            }
            85% {
                transform: translateY(-1vh);
                animation-timing-function: ease-in-out;
            }
            92% {
                transform: translateY(0.5vh) scaleY(101%);
                animation-timing-function: ease-out;
            }
            100% {
                transform: translateY(0);
                animation-timing-function: ease-out;
            }
            }
            
            .divError div {
                background: none;
            }

            .divBody {
                z-index: 20;
                background: none;
                position: fixed;
                width: 100%;
                height: 100vh;
                transform: translateY(108px);
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
                animation: animasiIndex 4s ease-in;
            }
            @keyframes animasiIndex {
                0% {
                    z-index: 0;
                } 80% {
                    z-index: 20;
                }
            }
            .divBody2 {
                display: flex;
                background: none;
            }
            .divRight {
                width: 151px;
                height: 84px;
                display: flex;
                flex-direction: column;
                padding: 28px;
                margin-left: 16px;
                background: #ffffffbf;
                border-radius: 2em;
                animation: animasiText1 5.6s ease-in;
            }
            .divRight h2 p {
                color: #bc1888;
                background: none;
            }
            @keyframes animasiText1 {
                0% {
                    position: absolute;
                    width: 0;
                    opacity: 0;
                }
                80% {
                    position: relative;
                    width: 0;
                    opacity: 0;
                }
                93% {
                    width: 154px;
                    opacity: 1;
                }
                95% {
                    width: 158px;
                }
                98% {
                    width: 153px;
                }
                100% {
                    width: 151px;
                }
            }

            .divRight h1 {
                background: none;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                overflow: hidden;
            }
            .divRight h2 {
                background: none;
                font-family: "Times New Roman", Times, serif;
                overflow: hidden;
            }
            .divLeft {
                background: none;
            }

            .divSample {
                background: linear-gradient(
                    45deg,
                    #f09433 0%,
                    #e6683c 25%,
                    #dc2743 50%,
                    #cc2366 75%,
                    #bc1888 100%
                );
                width: 200px;
                height: 200px;
                border-radius: 2em;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: animasi1 4s ease-in, animasi1x 1.2s 3.4s ease-in;
            }
            @keyframes animasi1 {
                0% {
                    transform: translate(-120vh) rotate(-640deg);
                    border-radius: 10em;
                }
                56% {
                    transform: translate(calc(100vh - 140px)) rotate(0deg);
                }
                57% {
                    transform: translate(calc(100vh - 141px)) scaleX(100%);
                }
                58% {
                    transform: translate(calc(100vh - 130px)) scaleX(90%);
                }
                61% {
                    transform: translate(calc(100vh - 114px)) scaleX(75%);
                }

                64% {
                    transform: translate(500px, -60px) scaleX(100%);
                }
                66% {
                    transform: translate(450px, -80px) scaleX(100%);
                }
                68% {
                    transform: translate(400px, -100px) scaleX(110%);
                }
                70% {
                    transform: translate(350px, -95px) scaleX(110%);
                }

                72% {
                    transform: translate(300px, -40px) scaleX(104%);
                }
                73.5% {
                    transform: translate(250px, -15px) scaleX(104%);
                }
                75% {
                    transform: translate(200px, -2px) scale(100%);
                }
                76% {
                    transform: translate(200px, 40px) scaleY(80%) rotate(180deg);
                }

                80% {
                    transform: translate(140px, -40px) scaleY(108%);
                }
                84% {
                    transform: translate(80px, -80px) scaleY(102%);
                }
                87% {
                    transform: translate(0, -2px) scale(100%);
                }
                88% {
                    transform: translate(0, 10px) scaleY(90%);
                }
                90% {
                    transform: translate(0, -20px) scale(100%);
                }
                94% {
                    transform: translate(0, 5px) scaleY(95%);
                }
                95% {
                    transform: translate(0, 0) scale(100%) rotate(0deg);
                }
                100% {
                    transform: translate(0, 0) scale(100%);
                    border-radius: 10em;
                }
            }
            @keyframes animasi1x {
                0% {
                    border-radius: 10em;
                }
                100% {
                    border-radius: 2em;
                }
            }

            .divSample div {
                background: none;
                width: 120px;
                height: 120px;
                border: 14px solid white;
                border-radius: 2.5em;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: animasi2 4.6s ease-in;
            }
            @keyframes animasi2 {
                0% {
                    transform: scale(0%);
                }
                75% {
                    transform: scale(0%) rotate(180deg);
                }
                84% {
                    border-radius: 10em;
                }
                100% {
                    transform: scale(100%);
                    border-radius: 2.5em;
                }
            }

            .divSample div div {
                width: 45px;
                height: 45px;
                border-radius: 10em;
                animation: animasi3 6s ease-in, animasi3x 0.3s 7.2s ease-in;
            }
            @keyframes animasi3 {
                0% {
                    transform: translate(0, 26px);
                }
                64% {
                    transform: translate(0, 26px) rotate(-45deg);
                }
                70% {
                    transform: translate(0, 36px) scaleY(80%) rotate(-180deg);
                }
                75% {
                    transform: translate(0, -26px) scaleY(110%) rotate(-184deg);
                }
                76% {
                    transform: translate(0, -26px) scaleY(110%) rotate(-180deg);
                }
                80% {
                    transform: translate(0, 0) scale(100%) rotate(90deg);
                }
                82% {
                    transform: translate(0, 0) scaleY(90%) rotate(120deg);
                }
                84% {
                    transform: translate(0, 0) scaleY(90%) rotate(90deg);
                }
                86% {
                    transform: translate(0, -14px) scaleY(105%);
                }
                89% {
                    transform: translate(0, 0) scaleY(96%);
                }
                92% {
                    transform: translate(0, 0) scale(100%);
                }
                100% {
                    transform: translate(0, 0) rotate(0deg);
                }
            }
            @keyframes animasi3x {
                50% {
                    background: #fcffe6;
                    width: calc(45px - 2 * (21px - 14px));
                    height: calc(45px - 2 * (21px - 14px));
                    border: 21px solid #e2e4d3;
                }
            }

            .divSample div div div {
                width: 20px;
                height: 20px;
                border-radius: 10em;
                background: white;
                border: 0;
                transform: translate(190%, -190%);
                animation: animasi4 6.7s ease-in, animasi4x 0.2s 7.2s ease-in;
            }
            @keyframes animasi4 {
                0% {
                    transform: translate(-50%, 50%);
                }
                66% {
                    transform: translate(-50%, 60%) rotate(45deg) scaleY(80%);
                }
                75% {
                    transform: translate(-50%, 50%) rotate(45deg) scaleY(100%);
                }
                79% {
                    transform: translate(228%, -228%) rotate(45deg) scaleY(120%);
                }
                80% {
                    transform: translate(154%, -154%) rotate(45deg) scaleY(80%);
                }
                81% {
                    transform: translate(160%, -160%) rotate(45deg) scaleY(90%);
                }
                84% {
                    transform: translate(230%, -230%) rotate(45deg) scaleY(96%);
                }
                88% {
                    transform: translate(180%, -180%);
                }
                92% {
                    transform: translate(196%, -196%);
                }
                96% {
                    transform: translate(186%, -186%);
                }
                100% {
                    transform: translate(190%, -190%);
                }
            }
            @keyframes animasi4x {
                50% {
                    background: #faffc9;
                    box-shadow: 0 0 24px #f6ffa9;
                }
            }
        </style>
    </body>
</html>
