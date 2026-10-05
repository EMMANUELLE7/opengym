import api from "../api";

export async function login(email, password) {
    const response = await api.post("/api/login", {
        email,
        password,
    });

    const token = response.data.token;

    localStorage.setItem("token", token);

    return response.data;
}