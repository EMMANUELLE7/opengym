import { useState } from "react";
import { createSeance } from "../services/seanceService";

function CreateSeance() {
    const [titre, setTitre] = useState("");
    const [description, setDescription] = useState("");
    const [dateHeure, setDateHeure] = useState("");
    const [duree, setDuree] = useState("");
    const [placesTotal, setPlacesTotal] = useState("");
    const [message, setMessage] = useState("");

    async function handleSubmit(event) {
        event.preventDefault();

        try {
            const data = await createSeance({
                titre,
                description,
                dateHeure,
                duree: Number(duree),
                placesTotal: Number(placesTotal),
            });

            setMessage(data.message || "Séance créée !");

            setTitre("");
            setDescription("");
            setDateHeure("");
            setDuree("");
            setPlacesTotal("");
        } catch (error) {
            console.error(error);

            setMessage(
                error.response?.data?.message ||
                "Impossible de créer la séance."
            );
        }
    }

    return (
        <div>
            <h2>Créer une séance</h2>

            <form onSubmit={handleSubmit}>
                <div>
                    <label>Titre</label>
                    <br />
                    <input
                        type="text"
                        value={titre}
                        onChange={(event) => setTitre(event.target.value)}
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
                    Créer la séance
                </button>
            </form>

            <p>{message}</p>
        </div>
    );
}

export default CreateSeance;