<?php
function renderTicketTemplate(string $full_name, string $email, string $company_name, string $booking_date, string $from, string $to, string $departure_date, string $arrival_date, string $seat_number, int $ticket_price, ?int $discount = null): string
{
    ob_start(); ?>

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                color: #000;
            }

            .ticket-container {
                border: 2px solid #000;
                padding: 20px;
                max-width: 800px;
                margin: 0 auto;
            }

            .header {
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }

            .header h1 {
                margin: 0 0 5px 0;
                font-size: 24px;
            }

            .header p {
                margin: 3px 0;
                font-size: 11px;
            }

            .booking-ref {
                border: 2px solid #000;
                padding: 8px;
                text-align: center;
                margin: 15px 0;
                font-size: 16px;
                font-weight: bold;
            }

            .section {
                margin: 15px 0;
            }

            .section-title {
                font-weight: bold;
                border-bottom: 1px solid #000;
                padding: 5px 0;
                margin-bottom: 10px;
                font-size: 14px;
            }

            .info-row {
                display: table;
                width: 100%;
                margin-bottom: 5px;
                font-size: 12px;
            }

            .info-label {
                display: table-cell;
                font-weight: bold;
                width: 40%;
                padding: 3px 0;
            }

            .info-value {
                display: table-cell;
                padding: 3px 0;
            }

            .seats {
                display: inline-block;
                border: 1px solid #000;
                padding: 3px 10px;
                margin-right: 5px;
                font-weight: bold;
            }

            .total-amount {
                border: 2px solid #000;
                padding: 10px;
                text-align: right;
                margin-top: 10px;
            }

            .total-amount .label {
                font-size: 12px;
            }

            .total-amount .amount {
                font-size: 18px;
                font-weight: bold;
            }

            .footer {
                border-top: 1px solid #000;
                padding-top: 10px;
                margin-top: 20px;
                text-align: center;
                font-size: 10px;
            }

            .important-notes {
                border: 1px solid #000;
                padding: 10px;
                margin: 15px 0;
            }

            .important-notes h4 {
                margin: 0 0 8px 0;
                font-size: 12px;
            }

            .important-notes ul {
                margin: 0;
                padding-left: 20px;
                font-size: 10px;
            }

            .important-notes li {
                margin: 3px 0;
            }

            .barcode {
                text-align: center;
                margin: 15px 0;
                padding: 15px;
                border: 1px dashed #000;
                font-size: 14px;
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class="ticket-container">
            <div class="header">
                <h1>Hızlı Bilet™</h1>
                <p>Bilet Bilgisi</p>
            </div>

            <div class="section">
                <div class="section-title">Yolcu Bilgileri</div>
                <div class="info-row">
                    <div class="info-label">İsim-Soyisim:</div>
                    <div class="info-value"><?php echo htmlspecialchars($full_name); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">E-posta:</div>
                    <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Satın Alım Tarihi:</div>
                    <div class="info-value"><?php echo htmlspecialchars($booking_date); ?></div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Yolculuk Detayları</div>
                <div class="info-row">
                    <div class="info-label">Rota:</div>
                    <div class="info-value"><?php echo htmlspecialchars($from); ?> → <?php echo htmlspecialchars($to) ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Firma:</div>
                    <div class="info-value"><?php echo htmlspecialchars($company_name); ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ayrılma Zamanı:</div>
                    <div class="info-value"><?php echo htmlspecialchars($departure_date); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Varış Zamanı:</div>
                    <div class="info-value"><?php echo htmlspecialchars($arrival_date); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Otobüs Tipi:</div>
                    <div class="info-value">2+1 Rahat</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Koltuk</div>
                    <div class="info-value">
                        <span class="seats"><?php echo htmlspecialchars($seat_number); ?></span>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Ödeme Detayları</div>
                <div class="info-row">
                    <div class="info-label">Bilet Fiyatı:</div>
                    <div class="info-value"><?php echo htmlspecialchars($ticket_price) . " TL"; ?></div>
                </div>

                <?php if ($discount): ?>
                    <div class="info-row">
                        <div class="info-label">İndirim:</div>
                        <div class="info-value"><?php echo htmlspecialchars($discount); ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="important-notes">
                <h4>Önemli Hatırlatmalar</h4>
                <ul>
                    <li>Lütfen ayrılma tarihinden 15dk önce durakta olunuz.</li>
                    <li>Lütfen kimliğinizi yanınızda bulundurunuz</li>
                    <li>Ayrılma saatinden 1 saat öncesine kadar biletinizi iptal edebilirsiniz</li>
                </ul>
            </div>

            <div class="footer">
                <p><strong>Biletinizi Hızlı Bilet™ ile aldığınız için teşekkür ederiz </strong></p>
                <p>Bu bilet otomatik olarak oluşturulmuştur.</p>
                <p>&copy; <?php echo date('Y'); ?> Hızlı Bilet™. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </body>

    </html>
    <?php $ticket = ob_get_clean();
    return $ticket ?>
<?php } ?>