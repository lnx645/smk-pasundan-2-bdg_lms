export interface Auth {
    user: User;
}
type Kelas = {
    nama_kelas?: string;
    id_kelas?: any;
    jumlah_siswa?: number;
};

export interface Siswa {
    nis: string;
    jenis_kelamin: string;
    agama: string;
    tahun_masuk: string;
    tingkat: number;
    pas_photo: string;
    status: string;
    user_id: number;
    created_at: string;
    updated_at: string;
    kelas_id: number;
    kelas: {
        id: number;
        nama: string;
        tingkat: number;
        created_at: string;
        updated_at: string;
    };
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    is_admin: number;
    created_at: string;
    updated_at: string;
    fcm_token: string | null;
    role: string;
    nama: string;
    siswa: Siswa;
}

export interface JawabanTugas {
    jawaban_id: number;
    file_url: string | null;
    created_at: string | null;

    user_id: number;
    user_name: string;

    nis: string;
    kelas_nama: string;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    tugas: any[];
    jawaban: JawabanTugas[];
    info_kelas: any;
    search_current_terms: any;
    matpels: Matpels[];
    materials: any[];
    materi: any;
    kelas: Kelas[];
};
export interface Matpels {
    kode_matpel: string;
    nama: string;
    kategori: string;
    matpel_kode: string;
    guru_nip: string;
    kelas_id: string;
    created_at: string;
    updated_at: string;
    nama_guru: string;
    nama_kelas: string;
    nama_matpel: string;
    nip: string;
    jenis_kelamin: string;
    status: string;
    gelar_depan: string;
    gelar_belakang: string;
    user_id: number;
}

export interface User {
    id: number;
    role: string;
    nama: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    kelas?: {
        nama: string;
        id: string;
    };
}
