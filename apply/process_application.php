<?php
include '../includes/db.php'; // Database connection
require '../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $camp_id = trim($_POST['camp_id']);
    $selected_date = trim($_POST['selected_date']);
    $parent_name = trim($_POST['parent_name']);
    $parent_address = trim($_POST['parent_address']);
    $parent_phone = trim($_POST['parent_phone']);
    $parent_email = trim($_POST['parent_email']);
    $parent_id_number = trim($_POST['parent_id_number']);
    $child_name = trim($_POST['child_name']);
    $child_dob = trim($_POST['child_dob']);
    $child_id_number = trim($_POST['child_id_number']);
    $billing_type = trim($_POST['billing_type']);
    $billing_name = isset($_POST['billing_name']) ? trim($_POST['billing_name']) : null;
    $billing_address = isset($_POST['billing_address']) ? trim($_POST['billing_address']) : null;
    $billing_company_name = isset($_POST['billing_company_name']) ? trim($_POST['billing_company_name']) : null;
    $billing_tax_number = isset($_POST['billing_tax_number']) ? trim($_POST['billing_tax_number']) : null;
    $billing_company_address = isset($_POST['billing_company_address']) ? trim($_POST['billing_company_address']) : null;
    $meal_option = isset($_POST['meal_option']) ? 1 : 0;
    $emergency_name_1 = trim($_POST['emergency_name_1']);
    $emergency_relationship_1 = trim($_POST['emergency_relationship_1']);
    $emergency_phone1 = trim($_POST['emergency_phone1']);
    $emergency_name_2 = trim($_POST['emergency_name_2']);
    $emergency_relationship_2 = trim($_POST['emergency_relationship_2']);
    $emergency_phone2 = trim($_POST['emergency_phone2']);
    $medical_notes = trim($_POST['medical_notes']);
    $contact_consent = isset($_POST['contact_consent']) ? 1 : 0;
    $photo_consent = isset($_POST['photo_consent']) ? 1 : 0;
    $health_consent = isset($_POST['health_consent']) ? 1 : 0;
    $liability_waiver = isset($_POST['liability_waiver']) ? 1 : 0;
    $aszf_acceptance = isset($_POST['aszf_acceptance']) ? 1 : 0;
    $guardian_confirmation = isset($_POST['guardian_confirmation']) ? 1 : 0;
    $additional_notes = trim($_POST['additional_notes']);

    // Validate camp_id and get prices
    $query = "SELECT price, meal_price FROM camps WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $camp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $camp = $result->fetch_assoc();
    $stmt->close();

    if (!$camp) {
        error_log("Invalid camp_id: $camp_id");
        echo "<script>alert('Érvénytelen tábor!'); window.history.back();</script>";
        exit;
    }

    // Calculate total price
    $base_price = $camp['price'];
    $meal_price = $camp['meal_price'];
    $total_price = $meal_option ? $base_price + $meal_price : $base_price;

    // Validation
    $required_fields = [
        'camp_id' => $camp_id,
        'selected_date' => $selected_date,
        'parent_name' => $parent_name,
        'parent_address' => $parent_address,
        'parent_phone' => $parent_phone,
        'parent_email' => $parent_email,
        'parent_id_number' => $parent_id_number,
        'child_name' => $child_name,
        'child_dob' => $child_dob,
        'child_id_number' => $child_id_number,
        'billing_type' => $billing_type,
        'emergency_name_1' => $emergency_name_1,
        'emergency_relationship_1' => $emergency_relationship_1,
        'emergency_phone1' => $emergency_phone1,
        'emergency_name_2' => $emergency_name_2,
        'emergency_relationship_2' => $emergency_relationship_2,
        'emergency_phone2' => $emergency_phone2,
    ];

    if ($billing_type === 'individual') {
        $required_fields['billing_name'] = $billing_name;
        $required_fields['billing_address'] = $billing_address;
    } else {
        $required_fields['billing_company_name'] = $billing_company_name;
        $required_fields['billing_tax_number'] = $billing_tax_number;
        $required_fields['billing_company_address'] = $billing_company_address;
    }

    foreach ($required_fields as $field_name => $value) {
        if (!isset($value) || $value === '') {
            error_log("Validation failed for field: $field_name, Value: '$value'");
            echo "<script>alert('Minden kötelező mezőt ki kell tölteni! ($field_name)'); window.history.back();</script>";
            exit;
        }
    }

    if (!filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Érvénytelen email cím!'); window.history.back();</script>";
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO applications (
        camp_id, selected_date, parent_name, parent_address, parent_phone, parent_email, parent_id_number,
        child_name, child_dob, child_id_number, billing_type, billing_name, billing_address,
        billing_company_name, billing_tax_number, billing_company_address, meal_option,
        emergency_name_1, emergency_relationship_1, emergency_phone1,
        emergency_name_2, emergency_relationship_2, emergency_phone2,
        medical_notes, contact_consent, photo_consent, health_consent,
        liability_waiver, aszf_acceptance, guardian_confirmation, additional_notes, total_price
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "issssssssssissississssssiiiiiiis",
        $camp_id,
        $selected_date,
        $parent_name,
        $parent_address,
        $parent_phone,
        $parent_email,
        $parent_id_number,
        $child_name,
        $child_dob,
        $child_id_number,
        $billing_type,
        $billing_name,
        $billing_address,
        $billing_company_name,
        $billing_tax_number,
        $billing_company_address,
        $meal_option,
        $emergency_name_1,
        $emergency_relationship_1,
        $emergency_phone1,
        $emergency_name_2,
        $emergency_relationship_2,
        $emergency_phone2,
        $medical_notes,
        $contact_consent,
        $photo_consent,
        $health_consent,
        $liability_waiver,
        $aszf_acceptance,
        $guardian_confirmation,
        $additional_notes,
        $total_price
    );
    if ($stmt->execute()) {
        // Send email notification
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom(SMTP_FROM, 'Galgatábor');
            $mail->addAddress(SMTP_TO);
            $mail->Subject = 'Új Jelentkezés Érkezett';
            $mail->Body = "Új jelentkezés érkezett:\n\n" .
                          "Tábor ID: $camp_id\n" .
                          "Választott időpont: $selected_date\n" .
                          "Szülő/Gondviselő adatai:\n" .
                          "Név: $parent_name\n" .
                          "Lakcím: $parent_address\n" .
                          "Telefonszám: $parent_phone\n" .
                          "Email: $parent_email\n" .
                          "Személyi azonosító: $parent_id_number\n\n" .
                          "Gyermek adatai:\n" .
                          "Név: $child_name\n" .
                          "Születési dátum: $child_dob\n" .
                          "Személyi azonosító: $child_id_number\n\n" .
                          "Számlázási adatok:\n" .
                          "Típus: " . ($billing_type === 'individual' ? 'Magánszemély' : 'Cég') . "\n";
            
            if ($billing_type === 'individual') {
                $mail->Body .= "Név: $billing_name\n" .
                               "Cím: $billing_address\n";
            } else {
                $mail->Body .= "Cégnév: $billing_company_name\n" .
                               "Adószám: $billing_tax_number\n" .
                               "Székhely: $billing_company_address\n";
            }

            $mail->Body .= "\nÉtkeztetés: " . ($meal_option ? 'Igen (' . number_format($meal_price, 0, ',', ' ') . ' Ft)' : 'Nem') . "\n\n" .
                           "Vészhelyzeti elérhetőségek:\n" .
                           "1. Név: $emergency_name_1\n" .
                           "Kapcsolat: $emergency_relationship_1\n" .
                           "Telefonszám: $emergency_phone1\n" .
                           "2. Név: $emergency_name_2\n" .
                           "Kapcsolat: $emergency_relationship_2\n" .
                           "Telefonszám: $emergency_phone2\n\n" .
                           "Egészségügyi megjegyzések: $medical_notes\n\n" .
                           "Hozzájárulások:\n" .
                           "Kapcsolatfelvétel: " . ($contact_consent ? 'Igen' : 'Nem') . "\n" .
                           "Fotó/videó: " . ($photo_consent ? 'Igen' : 'Nem') . "\n" .
                           "Egészségügyi adatok: " . ($health_consent ? 'Igen' : 'Nem') . "\n" .
                           "Felelősségvállalás: " . ($liability_waiver ? 'Igen' : 'Nem') . "\n" .
                           "ÁSZF elfogadás: " . ($aszf_acceptance ? 'Igen' : 'Nem') . "\n" .
                           "Gondviselői nyilatkozat: " . ($guardian_confirmation ? 'Igen' : 'Nem') . "\n\n" .
                           "További megjegyzések: $additional_notes\n\n" .
                           "Fizetendő összeg: " . number_format($total_price, 0, ',', ' ') . " Ft";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: {$mail->ErrorInfo}");
        }

        echo "<script>alert('Jelentkezés sikeresen leadva! Hamarosan e-mailben küldjük a proforma számlát a fizetési részletekkel.'); window.location.href = '/galgatabor/apply/apply.php?camp_id=$camp_id';</script>";
    } else {
        echo "<script>alert('Hiba történt, próbáld újra!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Érvénytelen kérés!'); window.history.back();</script>";
}
?>