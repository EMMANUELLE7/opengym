import { useEffect, useState } from "react";
import { getSeances } from "../services/seanceService";
import EditSeance from "./EditSeance";

function Seances() {
  const [seances, setSeances] = useState([]);
  const [erreur, setErreur] = useState("");
  const [seanceAModifier, setSeanceAModifier] = useState(null);

  async function chargerSeances() {
    try {
      const data = await getSeances();

      console.log(data.seances);

      setSeances(data.seances);
    } catch (error) {
      console.error(error);
      setErreur("Impossible de charger les séances.");
    }
  }

  useEffect(() => {
    chargerSeances();
  }, []);

  function handleUpdated() {
    setSeanceAModifier(null);
    chargerSeances();
  }

  if (erreur) {
    return <p>{erreur}</p>;
  }

  return (
    <div>
      <h2>Gestion des séances</h2>

      {seances.map((seance) => (
        <div key={seance.id}>
          <h3>{seance.titre}</h3>

          <p>Description : {seance.description || "Aucune description"}</p>

          <p>Date : {seance.dateHeure}</p>

          <p>Durée : {seance.duree} minutes</p>

          <p>Places : {seance.placesRestantes} restantes</p>

          <button onClick={() => setSeanceAModifier(seance.id)}>
            Modifier
          </button>

          {seanceAModifier === seance.id && (
            <EditSeance seanceId={seance.id} onUpdated={handleUpdated} />
          )}
        </div>
      ))}
    </div>
  );
}

export default Seances;
