<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Gestion des paiements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #172b4d;
        }
        .sidebar a {
            color: #e8eef7;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 5px;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #315fc0;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        }
        .page-title {
            color: #172b4d;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="text-white mb-4">Gestion Paiements</h5>
            <nav>
                <a href="#" class="active">Accueil</a>
                <a href="#">Paiements</a>
                <a href="#">Quittances</a>
                <a href="#">Utilisateurs</a>
            </nav>
        </aside>
        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Tableau de bord</h2>
                    <p class="text-secondary mb-0">
                        Bienvenue sur votre espace de gestion.
                    </p>
                </div>
                <span class="badge text-bg-light p-2">Administrateur</span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card p-3">
                        <p class="text-secondary mb-2">Paiements</p>
                        <h3>12</h3>
                        <small class="text-success">Paiements enregistrés</small>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card p-3">
                        <p class="text-secondary mb-2">Montant encaissé</p>
                        <h3>250 000 FCFA</h3>
                        <small class="text-success">Total des paiements</small>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card p-3">
                        <p class="text-secondary mb-2">Quittances</p>
                        <h3>8</h3>
                        <small class="text-secondary">Quittances générées</small>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card p-3">
                        <p class="text-secondary mb-2">Utilisateurs</p>
                        <h3>5</h3>
                        <small class="text-secondary">Comptes enregistrés</small>
                    </div>
                </div>
            </div>
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Derniers paiements</h5>
                    <a href="#" class="text-decoration-none">
                        Voir tout
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jean Dupont</td>
                                <td>50 000 FCFA</td>
                                <td>12/10/2026</td>
                                <td>
                                    <span class="badge text-bg-success">Payé</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Marie Laurent</td>
                                <td>25 000 FCFA</td>
                                <td>10/10/2026</td>
                                <td>
                                    <span class="badge text-bg-success">Payé</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Ahmed Traoré</td>
                                <td>15 000 FCFA</td>
                                <td>08/10/2026</td>
                                <td>
                                    <span class="badge text-bg-warning">En attente</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
