<!--<!DOCTYPE html>-->
<!--<html lang="ar">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>قارئ QR مع تكبير الكاميرا</title>-->
<!--    <style>-->
<!--        /* تعيين أسلوب العرض ليعمل على جميع الشاشات */-->
<!--        body {-->
<!--            font-family: Arial, sans-serif;-->
<!--            text-align: center;-->
<!--        }-->
<!---->
<!--        /* خصائص عنصر الفيديو */-->
<!--        #camera-stream {-->
<!--            width: 100%;-->
<!--            max-width: 1000px;-->
<!--            margin-top: 20px;-->
<!--            border: 2px solid #000;-->
<!--            transition: transform 0.3s ease;-->
<!--        }-->
<!---->
<!--        /* تحكم في تكبير الصورة */-->
<!--        #zoomControl {-->
<!--            margin-top: 20px;-->
<!--            text-align: center;-->
<!--        }-->
<!---->
<!--        /* عرض النتيجة أسفل الكاميرا */-->
<!--        #result {-->
<!--            margin-top: 20px;-->
<!--            font-size: 18px;-->
<!--        }-->
<!---->
<!--        /* تحكم في حجم مربع قراءة QR */-->
<!--        #reader {-->
<!--            margin: auto;-->
<!--        }-->
<!---->
<!--        /* تجعل الزر قابل للتفاعل */-->
<!--        input[type="range"] {-->
<!--            width: 80%;-->
<!--            max-width: 300px;-->
<!--        }-->
<!---->
<!--        /* زر تبديل الكاميرا */-->
<!--        #switchCameraBtn {-->
<!--            margin-top: 20px;-->
<!--            padding: 10px 20px;-->
<!--            background-color: #4CAF50;-->
<!--            color: white;-->
<!--            border: none;-->
<!--            cursor: pointer;-->
<!--            font-size: 16px;-->
<!--        }-->
<!---->
<!--        #switchCameraBtn:hover {-->
<!--            background-color: #45a049;-->
<!--        }-->
<!--    </style>-->
<!--</head>-->
<!--<body>-->
<!--<h1>قارئ QR مع تكبير الكاميرا</h1>-->
<!--<p id="result">النتيجة: <span id="qr-result">لا يوجد نتيجة حتى الآن</span></p>-->
<!--<div id="reader"></div>-->
<!---->
<!--<!-- فيديو لعرض الكاميرا -->-->
<!--<video id="camera-stream" autoplay muted playsinline></video>-->
<!---->
<!--<!-- تحكم في الزوم -->-->
<!--<div id="zoomControl">-->
<!--    <label for="zoomSlider">التحكم في الزوم:</label>-->
<!--    <input type="range" id="zoomSlider" min="1" max="3" step="0.1" value="1" />-->
<!--</div>-->
<!---->
<!--<!-- زر تبديل الكاميرا -->-->
<!--<button id="switchCameraBtn">تبديل الكاميرا</button>-->
<!---->
<!--<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>-->
<!--<script>-->
<!--    // مكان عرض النتيجة-->
<!--    const qrResultElement = document.getElementById('qr-result');-->
<!--    const cameraStream = document.getElementById('camera-stream');-->
<!--    const zoomSlider = document.getElementById('zoomSlider');-->
<!--    const switchCameraBtn = document.getElementById('switchCameraBtn');-->
<!---->
<!--    let currentStream;-->
<!--    let isBackCamera = true;  // الحالة الأولية هي الكاميرا الخلفية-->
<!--    let html5QrCode;-->
<!---->
<!--    // تهيئة قارئ QR-->
<!--    function onScanSuccess(decodedText, decodedResult) {-->
<!--        // عرض النتيجة عند قراءة QR Code-->
<!--        qrResultElement.textContent = decodedText;-->
<!--        console.log(`تم قراءة الكود: ${decodedText}`, decodedResult);-->
<!--    }-->
<!---->
<!--    function onScanFailure(error) {-->
<!--        // يمكن عرض الأخطاء هنا أثناء الفشل-->
<!--        console.warn(`خطأ أثناء القراءة: ${error}`);-->
<!--    }-->
<!---->
<!--    // محاولة الحصول على إذن الكاميرا-->
<!--    function startCamera(facingMode) {-->
<!--        navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } })-->
<!--            .then(function(stream) {-->
<!--                // إذا تمت الموافقة على الكاميرا-->
<!--                console.log("تم منح الإذن لاستخدام الكاميرا");-->
<!---->
<!--                // عرض الفيديو على الصفحة-->
<!--                cameraStream.srcObject = stream;-->
<!---->
<!--                // حفظ البث لتبديل الكاميرا لاحقًا-->
<!--                currentStream = stream;-->
<!---->
<!--                // تهيئة الكاميرا لقراءة QR-->
<!--                if (html5QrCode) {-->
<!--                    html5QrCode.stop(); // إيقاف الكاميرا الحالية إذا كانت تعمل-->
<!--                }-->
<!--                html5QrCode = new Html5Qrcode("reader");-->
<!--                html5QrCode.start(-->
<!--                    { facingMode: facingMode }, // اختيار الكاميرا-->
<!--                    {-->
<!--                        fps: 10,    // عدد الإطارات في الثانية-->
<!--                        qrbox: 250  // حجم المربع المخصص لقراءة QR-->
<!--                    },-->
<!--                    onScanSuccess,-->
<!--                    onScanFailure-->
<!--                ).catch(err => {-->
<!--                    console.error("تعذر تشغيل الكاميرا:", err);-->
<!--                });-->
<!--            })-->
<!--            .catch(function(error) {-->
<!--                // في حالة عدم منح الإذن-->
<!--                alert("لم يتم منح الإذن لاستخدام الكاميرا.");-->
<!--                console.error("لم يتم منح الإذن لاستخدام الكاميرا:", error);-->
<!--            });-->
<!--    }-->
<!---->
<!--    // بدء الكاميرا الخلفية بشكل افتراضي-->
<!--    startCamera("environment");-->
<!---->
<!--    // إضافة تأثير الزوم عند تحريك شريط التمرير-->
<!--    zoomSlider.addEventListener('input', function() {-->
<!--        const zoomLevel = zoomSlider.value;-->
<!--        cameraStream.style.transform = `scale(${zoomLevel})`;-->
<!--    });-->
<!---->
<!--    // وظيفة لتبديل الكاميرا بين الأمامية والخلفية-->
<!--    switchCameraBtn.addEventListener('click', function() {-->
<!--        isBackCamera = !isBackCamera;  // تبديل حالة الكاميرا-->
<!--        const facingMode = isBackCamera ? "environment" : "user";  // تحديد الكاميرا المطلوبة-->
<!--        if (currentStream) {-->
<!--            // إيقاف البث السابق-->
<!--            currentStream.getTracks().forEach(track => track.stop());-->
<!--        }-->
<!--        // بدء الكاميرا الجديدة-->
<!--        startCamera(facingMode);-->
<!--    });-->
<!--</script>-->
<!--</body>-->
<!--</html>-->

<!-- Index.html file -->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!---->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport"-->
<!--          content="width=device-width, initial-scale=1.0">-->
<!--    <link rel="stylesheet"-->
<!--          href="style.css">-->
<!--    <title>QR Code Scanner / Reader-->
<!--    </title>-->
<!--</head>-->
<!--<style>-->
<!--    /* style.css file*/-->
<!--    body {-->
<!--        display: flex;-->
<!--        justify-content: center;-->
<!--        margin: 0;-->
<!--        padding: 0;-->
<!--        height: 100vh;-->
<!--        box-sizing: border-box;-->
<!--        text-align: center;-->
<!--        background: rgb(128 0 0 / 66%);-->
<!--    }-->
<!--    .container {-->
<!--        width: 100%;-->
<!--        max-width: 500px;-->
<!--        margin: 5px;-->
<!--    }-->
<!---->
<!--    .container h1 {-->
<!--        color: #ffffff;-->
<!--    }-->
<!---->
<!--    .section {-->
<!--        background-color: #ffffff;-->
<!--        padding: 50px 30px;-->
<!--        border: 1.5px solid #b2b2b2;-->
<!--        border-radius: 0.25em;-->
<!--        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);-->
<!--    }-->
<!---->
<!--    #my-qr-reader {-->
<!--        padding: 20px !important;-->
<!--        border: 1.5px solid #b2b2b2 !important;-->
<!--        border-radius: 8px;-->
<!--    }-->
<!---->
<!--    #my-qr-reader img[alt="Info icon"] {-->
<!--        display: none;-->
<!--    }-->
<!---->
<!--    #my-qr-reader img[alt="Camera based scan"] {-->
<!--        width: 100px !important;-->
<!--        height: 100px !important;-->
<!--    }-->
<!---->
<!--    button {-->
<!--        padding: 10px 20px;-->
<!--        border: 1px solid #b2b2b2;-->
<!--        outline: none;-->
<!--        border-radius: 0.25em;-->
<!--        color: white;-->
<!--        font-size: 15px;-->
<!--        cursor: pointer;-->
<!--        margin-top: 15px;-->
<!--        margin-bottom: 10px;-->
<!--        background-color: #008000ad;-->
<!--        transition: 0.3s background-color;-->
<!--    }-->
<!---->
<!--    button:hover {-->
<!--        background-color: #008000;-->
<!--    }-->
<!---->
<!--    #html5-qrcode-anchor-scan-type-change {-->
<!--        text-decoration: none !important;-->
<!--        color: #1d9bf0;-->
<!--    }-->
<!---->
<!--    video {-->
<!--        width: 100% !important;-->
<!--        border: 1px solid #b2b2b2 !important;-->
<!--        border-radius: 0.25em;-->
<!--    }-->
<!---->
<!--</style>-->
<!---->
<!--<body>-->
<!--<div class="container">-->
<!--    <h1>Scan QR Codes</h1>-->
<!--    <div class="section">-->
<!--        <div id="my-qr-reader">-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--<script-->
<!--    src="https://unpkg.com/html5-qrcode">-->
<!--</script>-->
<!--<script>-->
<!---->
<!--    function domReady(fn) {-->
<!--        if (-->
<!--            document.readyState === "complete" ||-->
<!--            document.readyState === "interactive"-->
<!--        ) {-->
<!--            setTimeout(fn, 1000);-->
<!--        } else {-->
<!--            document.addEventListener("DOMContentLoaded", fn);-->
<!--        }-->
<!--    }-->
<!---->
<!--    domReady(function () {-->
<!---->
<!--        // If found you qr code-->
<!--        function onScanSuccess(decodeText, decodeResult) {-->
<!--            alert("You Qr is : " + decodeText, decodeResult);-->
<!--        }-->
<!---->
<!--        let htmlscanner = new Html5QrcodeScanner(-->
<!--            "my-qr-reader",-->
<!--            { fps: 10, qrbos: 250 }-->
<!--        );-->
<!--        htmlscanner.render(onScanSuccess);-->
<!--    });-->
<!--</script>-->
<!--</body>-->
<!---->
<!--</html>-->


<!---->
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!---->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <link rel="stylesheet" href="style.css">-->
<!--    <title>QR Code Scanner / Reader</title>-->
<!--</head>-->
<!--<style>-->
<!--    body {-->
<!--        display: flex;-->
<!--        justify-content: center;-->
<!--        margin: 0;-->
<!--        padding: 0;-->
<!--        height: 100vh;-->
<!--        box-sizing: border-box;-->
<!--        text-align: center;-->
<!--        background: rgb(128 0 0 / 66%);-->
<!--    }-->
<!--    .container {-->
<!--        width: 100%;-->
<!--        max-width: 500px;-->
<!--        margin: 5px;-->
<!--    }-->
<!---->
<!--    .container h1 {-->
<!--        color: #ffffff;-->
<!--    }-->
<!---->
<!--    .section {-->
<!--        background-color: #ffffff;-->
<!--        padding: 50px 30px;-->
<!--        border: 1.5px solid #b2b2b2;-->
<!--        border-radius: 0.25em;-->
<!--        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);-->
<!--    }-->
<!---->
<!--    #my-qr-reader {-->
<!--        padding: 20px !important;-->
<!--        border: 1.5px solid #b2b2b2 !important;-->
<!--        border-radius: 8px;-->
<!--    }-->
<!---->
<!--    button {-->
<!--        padding: 10px 20px;-->
<!--        border: 1px solid #b2b2b2;-->
<!--        outline: none;-->
<!--        border-radius: 0.25em;-->
<!--        color: white;-->
<!--        font-size: 15px;-->
<!--        cursor: pointer;-->
<!--        margin-top: 15px;-->
<!--        margin-bottom: 10px;-->
<!--        background-color: #008000ad;-->
<!--        transition: 0.3s background-color;-->
<!--    }-->
<!---->
<!--    button:hover {-->
<!--        background-color: #008000;-->
<!--    }-->
<!---->
<!--    video {-->
<!--        width: 100% !important;-->
<!--        border: 1px solid #b2b2b2 !important;-->
<!--        border-radius: 0.25em;-->
<!--    }-->
<!--</style>-->
<!---->
<!--<body>-->
<!--<div class="container">-->
<!--    <h1>Scan QR Codes</h1>-->
<!--    <div class="section">-->
<!--        <div id="my-qr-reader"></div>-->
<!--    </div>-->
<!--</div>-->
<!--<script src="https://unpkg.com/html5-qrcode"></script>-->
<!--<script>-->
<!--    function domReady(fn) {-->
<!--        if (-->
<!--            document.readyState === "complete" ||-->
<!--            document.readyState === "interactive"-->
<!--        ) {-->
<!--            setTimeout(fn, 1000);-->
<!--        } else {-->
<!--            document.addEventListener("DOMContentLoaded", fn);-->
<!--        }-->
<!--    }-->
<!---->
<!--    domReady(function () {-->
<!--        // عند مسح رمز QR بنجاح-->
<!--        function onScanSuccess(decodedText, decodedResult) {-->
<!--            alert("Your QR Code is: " + decodedText);-->
<!--        }-->
<!---->
<!--        // في حال حدوث خطأ أثناء المسح-->
<!--        function onScanFailure(error) {-->
<!--            console.warn(`QR Code Scan Error: ${error}`);-->
<!--        }-->
<!---->
<!--        // إعداد الماسح-->
<!--        let htmlscanner = new Html5QrcodeScanner(-->
<!--            "my-qr-reader",-->
<!--            {-->
<!--                fps: 10, // عدد الإطارات في الثانية-->
<!--                qrbox: { width: 250, height: 250 }, // إطار المسح-->
<!--            }-->
<!--        );-->
<!---->
<!--        // تشغيل الماسح-->
<!--        htmlscanner.render(onScanSuccess, onScanFailure);-->
<!--    });-->
<!--</script>-->
<!--</body>-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner / Attendance</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            box-sizing: border-box;
            text-align: center;
            background: rgb(128 0 0 / 66%);
        }

        .container {
            width: 100%;
            max-width: 500px;
            margin: 5px;
        }

        .container h1 {
            color: #ffffff;
        }

        .section {
            background-color: #ffffff;
            padding: 30px 20px;
            border: 1.5px solid #b2b2b2;
            border-radius: 0.25em;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);
        }

        #my-qr-reader {
            margin-bottom: 20px;
            border: 1.5px solid #b2b2b2 !important;
            border-radius: 8px;
        }

        button {
            padding: 10px 20px;
            border: 1px solid #b2b2b2;
            outline: none;
            border-radius: 0.25em;
            color: white;
            font-size: 15px;
            cursor: pointer;
            margin-top: 15px;
            background-color: gray;
            transition: 0.3s background-color;
        }

        button.active {
            background-color: #008000;
            cursor: pointer;
        }

        button:hover.active {
            background-color: #005500;
        }

        video {
            width: 100% !important;
            border: 1px solid #b2b2b2 !important;
            border-radius: 0.25em;
        }

        #result {
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Scan QR Code for Attendance</h1>
    <div class="section">
        <div id="my-qr-reader"></div>
        <button id="attendance-btn" disabled>تسجيل حضور</button>
        <div id="result">لم يتم مسح رمز QR بعد</div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner;
    let qrData = "";
    const attendanceBtn = document.getElementById("attendance-btn");
    const resultDiv = document.getElementById("result");

    function startScanner() {
        html5QrcodeScanner = new Html5Qrcode("my-qr-reader");

        // تشغيل المسح
        html5QrcodeScanner.start(
            { facingMode: "environment" }, // الكاميرا الخلفية
            {
                fps: 10, // عدد الإطارات في الثانية
                qrbox: { width: 250, height: 250 }, // حجم الإطار
            },
            onScanSuccess,
            onScanFailure
        ).catch((err) => {
            console.error("Error starting scanner:", err);
        });
    }

    // عند مسح QR بنجاح
    function onScanSuccess(decodedText, decodedResult) {
        qrData = decodedText;
        attendanceBtn.disabled = false;
        attendanceBtn.classList.add("active");
        resultDiv.textContent = `تم مسح رمز QR: ${qrData}`;

        html5QrcodeScanner.stop().then(() => {
            console.log("Scanner stopped after success.");
        }).catch((err) => {
            console.error("Error stopping scanner:", err);
        });
    }


    function onScanFailure(error) {
        console.warn(`QR Code Scan Error: ${error}`);
    }


    attendanceBtn.addEventListener("click", function () {
        if (!qrData) return;


        fetch("/attendance", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ qrData: qrData }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    resultDiv.textContent = "تم تسجيل الحضور بنجاح!";
                } else {
                    resultDiv.textContent = "حدث خطأ أثناء تسجيل الحضور.";
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                resultDiv.textContent = "حدث خطأ أثناء الإرسال.";
            });


        qrData = "";
        attendanceBtn.disabled = true;
        attendanceBtn.classList.remove("active");
        startScanner();
    });

    document.addEventListener("DOMContentLoaded", startScanner);
</script>
</body>

</html>
