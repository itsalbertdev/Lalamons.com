<?php
// profile.php

// Initial profile data
$full_name = "John Doe";
$email = "johndoe@example.com";
$phone = "123-456-7890";
$region = "";
$province = "";
$municipality = "";
$barangay = "";
$postal_code = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "lalamons_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch profile data including address details
$sql = "SELECT p.full_name, p.email, p.phone, a.region, a.province, a.municipality, a.barangay, a.postal_code, a.id as address_id
        FROM profile p
        JOIN address a ON p.address_id = a.id
        WHERE p.id = 1"; // Assuming you're updating the profile with id = 1
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $full_name = $row['full_name'];
    $email = $row['email'];
    $phone = $row['phone'];
    $region = $row['region'];
    $province = $row['province'];
    $municipality = $row['municipality'];
    $barangay = $row['barangay'];
    $postal_code = $row['postal_code'];
    $address_id = $row['address_id'];  // Store the current address_id
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $region = isset($_POST['region']) ? $_POST['region'] : '';
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $municipality = isset($_POST['municipality']) ? $_POST['municipality'] : '';
    $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : '';
    $postal_code = isset($_POST['postal_code']) ? $_POST['postal_code'] : '';

    // First, update the address table
    $stmt_address = $conn->prepare("UPDATE address SET region=?, province=?, municipality=?, barangay=?, postal_code=? WHERE id = ?");
    $stmt_address->bind_param("sssssi", $region, $province, $municipality, $barangay, $postal_code, $address_id);

    // Update the profile table
    $stmt_profile = $conn->prepare("UPDATE profile SET full_name=?, email=?, phone=?, address_id=? WHERE id=1");
    $stmt_profile->bind_param("sssi", $full_name, $email, $phone, $address_id);

    if ($stmt_address->execute() && $stmt_profile->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }
}

// Fetch regions
$regions = $conn->query("SELECT DISTINCT region FROM address WHERE region != ''");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .profile-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #d32f2f;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #d32f2f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.8;
        }
    </style>
    <script>
        // JavaScript to update dropdowns dynamically
        function updateProvinces() {
            const region = document.getElementById('region').value;
            const province = document.getElementById('province');
            const municipality = document.getElementById('municipality');
            const barangay = document.getElementById('barangay');
            const postal_code = document.getElementById('postal_code');

            province.innerHTML = '<option value="">Select a Province</option>';
            municipality.innerHTML = '<option value="">Select a Municipality</option>';
            barangay.innerHTML = '<option value="">Select a Barangay</option>';
            postal_code.value = ''; // Reset postal code

            if (region) {
                fetch(`get_provinces.php?region=${region}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.name;
                            option.textContent = item.name;
                            province.appendChild(option);
                        });
                    });
            }
        }

        function updateMunicipalities() {
            const province = document.getElementById('province').value;
            const municipality = document.getElementById('municipality');
            const barangay = document.getElementById('barangay');
            const postal_code = document.getElementById('postal_code');

            municipality.innerHTML = '<option value="">Select a Municipality</option>';
            barangay.innerHTML = '<option value="">Select a Barangay</option>';
            postal_code.value = ''; // Reset postal code

            if (province) {
                fetch(`get_municipalities.php?province=${province}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.name;
                            option.textContent = item.name;
                            municipality.appendChild(option);
                        });
                    });
            }
        }

        function updateBarangays() {
            const municipality = document.getElementById('municipality').value;
            const barangay = document.getElementById('barangay');
            const postal_code = document.getElementById('postal_code');

            barangay.innerHTML = '<option value="">Select a Barangay</option>';
            postal_code.value = ''; // Reset postal code

            if (municipality) {
                fetch(`get_barangays.php?municipality=${municipality}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.name;
                            option.textContent = item.name;
                            barangay.appendChild(option);
                        });
                    });
            }
        }

        function updatePostalCode() {
            const barangay = document.getElementById('barangay').value;
            const postal_code = document.getElementById('postal_code');

            if (barangay) {
                fetch(`get_postal_code.php?barangay=${barangay}`)
                    .then(response => response.json())
                    .then(data => {
                        postal_code.value = data.postal_code || ''; // Update postal code
                    });
            }
        }
    </script>
</head>
<body>
    <div class="profile-container">
        <h1>Profile</h1>
        <form action="" method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone); ?>" required>

            <label for="region">Region:</label>
            <select id="region" name="region" onchange="updateProvinces()" required>
                <option value="">Select a Region</option>
                <?php while($row = $regions->fetch_assoc()): ?>
                    <option value="<?= $row['region']; ?>" <?= $region === $row['region'] ? 'selected' : ''; ?>><?= $row['region']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="province">Province:</label>
            <select id="province" name="province" onchange="updateMunicipalities()" required>
                <option value="">Select a Province</option>
            </select>

            <label for="municipality">Municipality:</label>
            <select id="municipality" name="municipality" onchange="updateBarangays()" required>
                <option value="">Select a Municipality</option>
            </select>

            <label for="barangay">Barangay:</label>
            <select id="barangay" name="barangay" onchange="updatePostalCode()" required>
                <option value="">Select a Barangay</option>
            </select>

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($postal_code); ?>" required>

            <button type="submit">Save</button>
        </form>
    </div>
</body>
</html>