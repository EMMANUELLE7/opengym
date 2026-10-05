import { useState } from "react";
import { login } from "./services/authService";
import Seances from "./components/Seances";
import CreateSeance from "./components/CreateSeance";

function App() {
    const [connecte, setConnecte] = useState(false);
    const [message, setMessage] = useState("");

    async function handleLogin() {
        try {
            await login(
                "coach@opengym.test",
                "Coach123!"
            );

            setConnecte(true);
            setMessage("Connexion réussie !");
        } catch (error) {
            console.error(error);
            setMessage("Erreur de connexion");
        }
    }

    return (
        <div>
            <h1>OpenGym - Espace Coach</h1>

            {!connecte && (
                <button onClick={handleLogin}>
                    Se connecter
                </button>
            )}

            <p>{message}</p>

            {connecte && (
                <>
                    <CreateSeance />
                    <Seances />
                </>
            )}
        </div>
    );
}

export default App;