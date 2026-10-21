<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quittance de paiement</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .quittance {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }

        .receipt-header {
            border-bottom: 2px solid #172b4d;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .receipt-title {
            color: #172b4d;
            letter-spacing: 1px;
        }

        .amount {
            background: #f0f5ff;
            padding: 20px;
            border-radius: 8px;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .quittance {
                max-width: 100%;
                margin: 0;
                padding: 15px;
                border: none;
                border-radius: 0;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="quittance">

        <!-- En-tête -->
        <div class="receipt-header d-flex justify-content-between">
            <div>
                <h4 class="fw-bold">MON ENTREPRISE</h4>
                <p class="text-secondary mb-1">
                    Adresse de l'entreprise
                </p>
                <p class="text-secondary mb-0">
                    Téléphone : 6XX XXX XXX
                </p>
            </div>

            <div class="text-end">
                <h5 class="receipt-title fw-bold">
                    QUITTANCE DE PAIEMENT
                </h5>
                <p class="mb-1">N° Q-2026-001</p>
                <p class="text-secondary mb-0">
                    Date : 21/10/2026
                </p>
            </div>
        </div>

        <!-- Informations du payeur -->
        <h6 class="fw-bold mb-3">INFORMATIONS DU PAYEUR</h6>

        <div class="row mb-4">
            <div class="col-sm-6 mb-3">
                <p class="text-secondary mb-1">Nom complet</p>
                <p class="fw-semibold">Jean Dupont</p>
            </div>

            <div class="col-sm-6 mb-3">
                <p class="text-secondary mb-1">Référence du paiement</p>
                <p class="fw-semibold">PAY-2026-001</p>
            </div>
        </div>

        <!-- Détails du paiement -->
        <h6 class="fw-bold mb-3">DÉTAILS DU PAIEMENT</h6>

        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th>Description</th>
                    <th>Mode de paiement</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Règlement de prestation</td>
                    <td>Mobile Money</td>
                    <td>21/10/2026</td>
                </tr>
            </tbody>
        </table>

        <!-- Montant -->
        <div class="amount mb-4">
            <p class="text-secondary mb-1">Montant payé</p>
            <h3 class="fw-bold mb-0">50 000 FCFA</h3>
        </div>

        <p>
            La présente quittance atteste la réception du montant indiqué
            ci-dessus au titre du paiement décrit.
        </p>

        <div class="row mt-5">
            <div class="col-6">
                <p class="fw-semibold">Signature du payeur</p>
                <div style="height: 60px;"></div>
            </div>

            <div class="col-6 text-end">
                <p class="fw-semibold">Signature du responsable</p>
                <div style="height: 60px;"></div>
            </div>
        </div>

        <!-- Bouton d'impression -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()"
                    class="btn btn-primary px-4">
                Imprimer la quittance
            </button>

            <button onclick="window.history.back()"
                    class="btn btn-outline-secondary px-4">
                Retour
            </button>
        </div>

    </div>
</div>

</body>
</html>