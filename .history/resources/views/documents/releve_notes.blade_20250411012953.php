<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relevé de Notes - {{ $user->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .footer { margin-top: 30px; font-size: 0.8em; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Université XYZ</h2>
        <h3>Relevé de Notes</h3>
    </div>

    <div class="student-info">
        <p><strong>Étudiant:</strong> {{ $user->name }}</p>
        <p><strong>Filière:</strong> {{ $user->filiere }}</p>
        <p><strong>Niveau:</strong> {{ $user->niveau }}</p>
        <p><strong>Date d'émission:</strong> {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Note</th>
                <th>Crédits</th>
            </tr>
        </thead>
        <tbody>
            <!-- Exemple de données statiques -->
            <tr>
                <td>Mathématiques</td>
                <td>16.5</td>
                <td>5</td>
            </tr>
            <tr>
                <td>Informatique</td>
                <td>18.0</td>
                <td>6</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré électroniquement - valable sans signature</p>
        <p>© {{ date('Y') }} Université XYZ</p>
    </div>
</body>
</html>