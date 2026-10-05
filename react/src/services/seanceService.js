import api from "../api";

export async function getSeances() {
    const response = await api.get("/api/seances");

    return response.data;
}

export async function getSeance(id) {
    const response = await api.get(`/api/seances/${id}`);

    return response.data;
}

export async function createSeance(seance) {
    const response = await api.post(
        "/api/seances",
        seance
    );

    return response.data;
}

export async function updateSeance(id, seance) {
    const response = await api.put(
        `/api/seances/${id}`,
        seance
    );

    return response.data;
}

export async function deleteSeance(id) {
    const response = await api.delete(
        `/api/seances/${id}`
    );

    return response.data;
}

export async function getParticipants(id) {
    const response = await api.get(
        `/api/seances/${id}/participants`
    );

    return response.data;
}