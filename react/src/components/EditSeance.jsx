import { useEffect, useState } from "react";
import {
    getSeance,
    updateSeance,
} from "../services/seanceService";

function EditSeance({ seanceId, onUpdated }) {
    const [titre, setTitre] = useState("");
    const [description, setDescription] = useState("");
    const [dateHeure, setDateHeure] = useState("");
    const [duree, setDuree] = useState("");
    const [placesTotal, setPlacesTotal] = useState("");
    const [message, setMessage] = useState("");

    useEffect(() => {
        async function chargerSeance() {
            try {
                const data = await getSeance(seanceId);

                const seance = data.seance || data;

                setTitre(seance.titre || "");
                setDescription(seance.description || "");
                setDateHeure(
                    seance.dateHeure
                        ? seance.dateHeure.slice(0, 16)
                        : ""
                );
                setDuree(seance.duree || "");
                setPlacesTotal(seance.placesTotal || "");
            } catch (error) {
                console.error(error);

                setMessage(
                    "Impossible de charger la séance."
                );
            }
        }

        if (seanceId) {
            chargerSeance();
        }
    }, [seanceId]);

    async function handleSubmit(event) {
        event.preventDefault();

        try {
            const data = await updateSeance(seanceId, {
                titre,
                description,
                dateHeure,
                duree: Number(duree),
                placesTotal: Number(placesTotal),
            });

            setMessage(
                data.message || "Séance modifiée !"
            );

            if (onUpdated) {
                onUpdated();
            }
        } catch (error) {
            console.error(error);

            setMessage(
                error.response?.data?.message ||
                "Impossible de modifier la séance."
            );
        }
    }

    return (
        <div>
            <h2>Modifier la séance</h2>

            <form onSubmit={handleSubmit}>
                <div>
                    <label>Titre</label>
                    <br />

                    <input
                        type="text"
                        value={titre}
                        onChange={(event) =>
                            setTitre(event.target.value)
                        }
                        required
                    />
                </div>

                <div>
                    <label>Description</label>
                    <br />

                    <textarea
                        value={description}
                        onChange={(event) =>
                            setDescription(event.target.value)
                        }
                    />
                </div>

                <div>
                    <label>Date et heure</label>
                    <br />

                    <input
                        type="datetime-local"
                        value={dateHeure}
                        onChange={(event) =>
                            setDateHeure(event.target.value)
                        }
                        required
                    />
                </div>

                <div>
                    <label>Durée en minutes</label>
                    <br />

                    <input
                        type="number"
                        value={duree}
                        onChange={(event) =>
                            setDuree(event.target.value)
                        }
                        required
                    />
                </div>

                <div>
                    <label>Nombre de places</label>
                    <br />

                    <input
                        type="number"
                        value={placesTotal}
                        onChange={(event) =>
                            setPlacesTotal(event.target.value)
                        }
                        required
                    />
                </div>

                <button type="submit">
                    Enregistrer les modifications
                </button>
            </form>

            <p>{message}</p>
        </div>
    );
}

export default EditSeance;