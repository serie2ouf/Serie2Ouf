// Outil 1
function calculerAutonomie() {
  const capacite = parseFloat(document.getElementById("capacite").value);
  const consommation = parseFloat(document.getElementById("consommation").value);
  const autonomie = capacite / consommation;
  document.getElementById("resultatAutonomie").textContent =
    `Votre véhicule peut parcourir environ ${autonomie.toFixed(1)} km !`;
}

// Outil 2

function comparerCouts() {
  const coutElectricite = parseFloat(document.getElementById("coutElectricite").value);
  const distanceJour = parseFloat(document.getElementById("distanceJour").value);
  const coutEssence = parseFloat(document.getElementById("coutEssence").value);
  const consommationScooter = parseFloat(document.getElementById("consommationScooter").value);

  // Coût annuel électrique (0.015 kWh/km pour une trottinette moyenne)
  const coutAnnuelElectrique = (distanceJour * 365 * 0.015 * coutElectricite).toFixed(2);
  // Coût annuel essence
  const coutAnnuelEssence = (distanceJour * 365 * (consommationScooter / 100) * coutEssence).toFixed(2);
  const economie = (coutAnnuelEssence - coutAnnuelElectrique).toFixed(2);

  document.getElementById("resultatComparaison").innerHTML = `
    <p>Coût annuel électrique : ${coutAnnuelElectrique} €</p>
    <p>Coût annuel essence : ${coutAnnuelEssence} €</p>
    <p>Économie avec l'électrique : ${economie} €/an !</p>
  `;
}

// Outil 3
function simulerFinancement() {
  // Récupération des valeurs
  const prix = parseFloat(document.getElementById("prix").value);
  const apport = parseFloat(document.getElementById("apport").value);
  const duree = parseInt(document.getElementById("duree").value);
  const tauxAnnuel = parseFloat(document.getElementById("taux").value) / 100;

  // Calculs de base
  const montantEmprunt = prix - apport;
  const tauxMensuel = tauxAnnuel / 12;
  const mensualite = (montantEmprunt * tauxMensuel) / (1 - Math.pow(1 + tauxMensuel, -duree));
  const coutTotal = (mensualite * duree).toFixed(2);
  const interetsTotaux = (coutTotal - montantEmprunt).toFixed(2);

  // Génération du tableau d'amortissement
  let tableauHTML = `
    <h3>Résultats du financement</h3>
    <p>Mensualité : <strong>${mensualite.toFixed(2)} €</strong></p>
    <p>Coût total : <strong>${coutTotal} €</strong> (dont ${interetsTotaux} € d'intérêts)</p>
    <br>
    <h4>Tableau d'amortissement :</h4>
    <table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">
      <tr>
        <th>Mois</th>
        <th>Mensualité (€)</th>
        <th>Intérêts (€)</th>
        <th>Capital remboursé (€)</th>
        <th>Capital restant (€)</th>
      </tr>
  `;

  let capitalRestant = montantEmprunt;

  for (let mois = 1; mois <= duree; mois++) {
    const interets = capitalRestant * tauxMensuel;
    const capitalRembourse = mensualite - interets;
    capitalRestant -= capitalRembourse;

    // Éviter les arrondis qui pourraient laisser un solde négatif
    if (mois === duree) {
      capitalRestant = 0;
    }

    tableauHTML += `
      <tr>
        <td>${mois}</td>
        <td>${mensualite.toFixed(2)}</td>
        <td>${interets.toFixed(2)}</td>
        <td>${capitalRembourse.toFixed(2)}</td>
        <td>${capitalRestant.toFixed(2)}</td>
      </tr>
    `;
  }

  tableauHTML += `</table>`;
  document.getElementById("resultatFinancement").innerHTML = tableauHTML;
}


// Outil 4
  function calculerTpsRecharge() {
        // Récupération des valeurs
        const capaciteTotale = parseInt(document.getElementById("modele").value);
        const puissance = parseFloat(document.getElementById("puissance").value);
        const niveauCharge = parseFloat(document.getElementById("niveauCharge").value);
        const tarif = parseFloat(document.getElementById("tarif").value);
        const resultat = document.getElementById("resultat");

        // Vérification des erreurs
        if (!capaciteTotale || isNaN(puissance) || isNaN(niveauCharge) || isNaN(tarif) ||
            niveauCharge < 0 || niveauCharge > 100) {
            resultat.innerHTML = "<div class='erreur'>Veuillez remplir tous les champs correctement (niveau de charge entre 0 et 100%).</div>";
            resultat.style.display = "block";
            return;
        }

        // Calcul de la capacité restante à charger
        const capaciteRestante = capaciteTotale * (1 - niveauCharge/100);

        // Calculs
        const tempsHeures = capaciteRestante / puissance;
        const cout = (capaciteRestante / 1000) * tarif;

        // Affichage
        const nomProduit = document.getElementById("modele").options[document.getElementById("modele").selectedIndex].text;

        resultat.innerHTML = `
            <h3>Résultats pour ${nomProduit}</h3>
            <p>Capacité à recharger : <strong>${capaciteRestante.toFixed(0)} Wh</strong> (${niveauCharge}% déjà chargé)</p>
            <p>Temps de recharge : <strong>${Math.floor(tempsHeures)}h${Math.round((tempsHeures % 1) * 60)}min</strong></p>
            <p>Coût de recharge : <strong>${cout.toFixed(2)} €</strong></p>
        `;
        resultat.style.display = "block";
    }

