import api from "../api";

export async function reserverSeance(seanceId) {
    const response = await api.post(
        `/api/seances/${seanceId}/reservations`
    );

    return response.data;
}