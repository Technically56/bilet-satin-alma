<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bize Ulaşın</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("includes/navbar.php"); ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="text-center mb-5 mt-5">
                    <h1 class="display-4 fw-bold">Bize Ulaşın</h1>
                    <p class="lead text-muted">Bir sorunuz mu var? Sizden haber almaktan mutluluk duyarız. Bize mesaj
                        gönderin, en kısa sürede yanıt verelim.</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">Ad</label>
                                    <input type="text" class="form-control" id="firstName" placeholder="Ahmet" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Soyad</label>
                                    <input type="text" class="form-control" id="lastName" placeholder="Yılmaz" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input type="email" class="form-control" id="email"
                                    placeholder="ahmet.yilmaz@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon Numarası</label>
                                <input type="tel" class="form-control" id="phone" placeholder="0555 123 45 67">
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Konu</label>
                                <input type="text" class="form-control" id="subject"
                                    placeholder="Size nasıl yardımcı olabiliriz?" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Mesaj</label>
                                <textarea class="form-control" id="message" rows="5"
                                    placeholder="Mesajınızı buraya yazın..." required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Mesaj Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row mt-5 g-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    class="bi bi-geo-alt-fill text-primary" viewBox="0 0 16 16">
                                    <path
                                        d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg>
                            </div>
                            <h5 class="fw-bold">Adres</h5>
                            <p class="text-muted">Atatürk Bulvarı No: 123<br>Kat 5, Daire 10<br>Samsun, Türkiye</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    class="bi bi-telephone-fill text-primary" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                                </svg>
                            </div>
                            <h5 class="fw-bold">Telefon</h5>
                            <p class="text-muted">0362 123 45 67<br>Hafta içi 09:00-18:00</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    class="bi bi-envelope-fill text-primary" viewBox="0 0 16 16">
                                    <path
                                        d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z" />
                                </svg>
                            </div>
                            <h5 class="fw-bold">E-posta</h5>
                            <p class="text-muted">info@hizlibilet.com<br>destek@hizlibilet.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <?php include("includes/footer.php"); ?>
</body>

</html>