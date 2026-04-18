function startTugas(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    Swal.fire({
        title: 'Mulai Kerjakan Tugas?',
        text: "Aksi ini akan mengubah status tugas menjadi 'PROSES'.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a', // emerald-600
        cancelButtonColor: '#52525b', // zinc-600
        confirmButtonText: 'Ya, Mulai Kerjakan!',
        cancelButtonText: 'Batal',
        background: '#1e293b', // slate-800
        color: '#e2e8f0' // slate-200
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memulai tugas...',
                background: '#1e293b', // slate-800
                color: '#e2e8f0', // slate-200
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/operator/produksi/${id}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Network response was not ok.') });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Tugas telah dimulai.',
                            background: '#1e293b',
                            color: '#e2e8f0',
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = `/operator/tugas/${id}`; // Redirect ke halaman detail
                        });
                    } else {
                        throw new Error(data.message || 'Gagal memulai tugas.');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message || 'Terjadi kesalahan.',
                        background: '#1e293b',
                        color: '#e2e8f0',
                    });
                });
        }
    });
}