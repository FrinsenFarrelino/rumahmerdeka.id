<?php
// File: backoffice/pilih_unit.php
require_once '../config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$sql = "SELECT p.id, p.nama_karyawan, p.nik_karyawan, p.nomor_induk_karyawan, us.blok, us.no_unit 
        FROM pendaftar p 
        LEFT JOIN unit_stock us ON p.id_unit = us.id
        WHERE p.status_proses = 'Terbit SP3K'
        ORDER BY p.nama_karyawan ASC";
$result = $conn->query($sql);
?>

<!-- Area Notifikasi -->
<div id="notification-bar" class="hidden fixed top-5 right-5 z-[100] p-4 rounded-lg shadow-lg text-white transition-transform duration-300 translate-x-[120%]" role="alert">
    <span id="notification-message"></span>
</div>

<h2 class="text-3xl font-semibold text-gray-700 mb-6">Pilih Unit untuk Peserta</h2>
<p class="text-gray-600 mb-6">Daftar peserta dengan status "Terbit SP3K" yang siap untuk memilih unit.</p>

<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Nama Karyawan</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">NIK / NIP</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Unit Terpilih</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($row['nama_karyawan']); ?></td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p>NIK: <?php echo htmlspecialchars($row['nik_karyawan']); ?></p>
                            <p>NIP: <?php echo htmlspecialchars($row['nomor_induk_karyawan']); ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm font-semibold">
                            <?php echo $row['blok'] ? htmlspecialchars($row['blok'] . ' / ' . $row['no_unit']) : '<span class="text-gray-400">Belum Memilih</span>'; ?>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                            <button class="open-modal-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                    data-participant-id="<?php echo $row['id']; ?>"
                                    data-participant-name="<?php echo htmlspecialchars($row['nama_karyawan']); ?>">
                                <?php echo $row['blok'] ? 'Ubah Unit' : 'Pilih Unit'; ?>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center py-10 text-gray-500">Tidak ada peserta yang siap memilih unit.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Pilih Unit dengan UI Filter Baru -->
<div id="unit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl transform transition-all">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-semibold">Pilih Unit untuk <span id="modal-participant-name" class="text-blue-600"></span></h3>
            <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div class="p-6 space-y-6">
            <!-- Area Filter -->
            <div class="border border-gray-200 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-3">Filter Unit Tersedia</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div id="filter-tipe-container"></div>
                    <div id="filter-status-container"></div>
                    <div id="filter-blok-container"></div>
                </div>
            </div>
            <!-- Area Pilihan -->
            <div class="pt-4">
                <label for="unit-selection" class="block text-sm font-bold text-gray-800 mb-2">Pilih Unit Tersedia</label>
                <select id="unit-selection" class="block w-full p-2 border border-gray-300 rounded-md bg-gray-50"></select>
            </div>
        </div>
        <div class="p-4 bg-gray-50 flex justify-end gap-4">
            <button id="cancel-btn" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Kembali</button>
            <button id="next-step-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400" disabled>Lanjut</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi (tetap sama) -->
<div id="confirm-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        <div class="p-6 border-b"><h3 class="text-xl font-semibold text-yellow-600">Konfirmasi Akhir</h3></div>
        <div id="confirm-details" class="p-6 text-center text-gray-700"></div>
        <div class="p-4 bg-gray-50 flex justify-end gap-4">
            <button id="back-to-select-btn" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Kembali</button>
            <button id="confirm-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Benar</button>
        </div>
    </div>
</div>

<script>
// [UPDATE v2.2] JavaScript Lengkap dengan Perbaikan Bug
document.addEventListener('DOMContentLoaded', () => {
    // Elemen Notifikasi
    const notificationBar = document.getElementById('notification-bar');
    const notificationMessage = document.getElementById('notification-message');
    let notificationTimeout;

    // Elemen Modal Utama
    const unitModal = document.getElementById('unit-modal');
    const openModalButtons = document.querySelectorAll('.open-modal-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const nextStepBtn = document.getElementById('next-step-btn');
    const modalParticipantName = document.getElementById('modal-participant-name');
    
    // Elemen Modal Konfirmasi
    const confirmModal = document.getElementById('confirm-modal');
    const confirmDetails = document.getElementById('confirm-details');
    const backToSelectBtn = document.getElementById('back-to-select-btn');
    const confirmBtn = document.getElementById('confirm-btn');

    // Elemen Pilihan & Filter
    const unitSelection = document.getElementById('unit-selection');
    const filterContainers = {
        tipe: document.getElementById('filter-tipe-container'),
        status: document.getElementById('filter-status-container'),
        blok: document.getElementById('filter-blok-container'),
    };

    let currentParticipantId = null;
    let currentParticipantName = null;
    let selectedUnitData = null;
    let activeFilters = { tipes: [], statuses: [], bloks: [] };

    // --- FUNGSI NOTIFIKASI ---
    const showNotification = (message, type = 'success') => {
        clearTimeout(notificationTimeout);
        notificationMessage.textContent = message;
        notificationBar.className = 'fixed top-5 right-5 z-[100] p-4 rounded-lg shadow-lg text-white transition-transform duration-300'; // Reset classes
        
        if (type === 'success') {
            notificationBar.classList.add('bg-green-500');
        } else {
            notificationBar.classList.add('bg-red-500');
        }
        
        notificationBar.classList.remove('hidden', 'translate-x-[120%]');
        notificationBar.classList.add('translate-x-0');

        notificationTimeout = setTimeout(() => {
            notificationBar.classList.add('translate-x-[120%]');
        }, 5000);
    };

    // --- FUNGSI UI FILTER MULTI-SELECT ---
    const createMultiSelect = (key, label, container) => {
        container.innerHTML = `
            <label class="block text-sm font-medium text-gray-700">${label}</label>
            <div class="relative mt-1">
                <div class="p-1 border border-gray-300 rounded-md bg-white flex flex-wrap gap-1 min-h-[38px] items-center">
                    <div class="selected-pills flex flex-wrap gap-1"></div>
                    <input type="text" class="flex-grow p-1 focus:outline-none text-sm" placeholder="Pilih...">
                </div>
                <div class="options-list hidden absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-48 overflow-y-auto shadow-lg"></div>
            </div>
        `;

        const pillsContainer = container.querySelector('.selected-pills');
        const input = container.querySelector('input');
        const optionsList = container.querySelector('.options-list');
        // [FIX v2.2] Penanganan kunci 'status' yang benar
        const filterKey = key === 'status' ? 'statuses' : `${key}s`;

        const renderPills = () => {
            pillsContainer.innerHTML = '';
            const filtersToRender = activeFilters[filterKey] || [];
            filtersToRender.forEach(value => {
                const pill = document.createElement('div');
                pill.className = 'bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full flex items-center gap-2';
                pill.innerHTML = `<span>${value}</span><button data-value="${value}" class="remove-pill-btn font-bold text-blue-800 hover:text-blue-900">&times;</button>`;
                pillsContainer.appendChild(pill);
            });
        };
        
        const renderOptions = (options) => {
            optionsList.innerHTML = '';
            const currentFilters = activeFilters[filterKey] || [];
            const availableOptions = (options || []).filter(opt => !currentFilters.includes(opt));
            
            if (availableOptions.length === 0) {
                 optionsList.innerHTML = `<div class="p-2 text-sm text-gray-500">Tidak ada pilihan</div>`;
            } else {
                availableOptions.forEach(option => {
                    const optionEl = document.createElement('div');
                    optionEl.className = 'p-2 text-sm cursor-pointer hover:bg-gray-100';
                    optionEl.textContent = option;
                    optionEl.dataset.value = option;
                    optionsList.appendChild(optionEl);
                });
            }
        };

        input.addEventListener('focus', () => optionsList.classList.remove('hidden'));
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                optionsList.classList.add('hidden');
            }
        });

        optionsList.addEventListener('click', (e) => {
            if (e.target.dataset.value) {
                // [FIX v2.2] Memastikan array ada sebelum push
                if (!activeFilters[filterKey]) {
                    activeFilters[filterKey] = [];
                }
                activeFilters[filterKey].push(e.target.dataset.value);
                input.value = '';
                optionsList.classList.add('hidden');
                fetchAndRender();
            }
        });

        pillsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-pill-btn')) {
                const valueToRemove = e.target.dataset.value;
                activeFilters[filterKey] = activeFilters[filterKey].filter(v => v !== valueToRemove);
                fetchAndRender();
            }
        });

        return { renderPills, renderOptions };
    };

    const filterUIs = {
        tipe: createMultiSelect('tipe', 'Filter Tipe', filterContainers.tipe),
        status: createMultiSelect('status', 'Filter Status', filterContainers.status),
        blok: createMultiSelect('blok', 'Filter Blok', filterContainers.blok),
    };

    // --- FUNGSI UTAMA ---
    const fetchAndRender = async () => {
        const params = new URLSearchParams({
            tipes: activeFilters.tipes.join(','),
            statuses: activeFilters.statuses.join(','),
            bloks: activeFilters.bloks.join(','),
        });

        try {
            const response = await fetch(`api/get_units.php?${params}`);
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }
            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }
            
            const filterOptions = data.filter_options || {};
            const units = data.units || [];

            filterUIs.tipe.renderPills();
            filterUIs.status.renderPills();
            filterUIs.blok.renderPills();

            // [FIX v2.2] Ganti nama kunci dari 'status_unit' ke 'status' agar cocok
            filterUIs.tipe.renderOptions(filterOptions.tipe || []);
            filterUIs.status.renderOptions(filterOptions.status_unit || []);
            filterUIs.blok.renderOptions(filterOptions.blok || []);

            unitSelection.innerHTML = '';
            if (units.length === 0) {
                unitSelection.innerHTML = '<option value="">Tidak ada unit yang cocok dengan filter</option>';
            } else {
                unitSelection.innerHTML = '<option value="">-- Pilih Unit dari Hasil Filter --</option>';
                units.forEach(unit => {
                    const opt = document.createElement('option');
                    opt.value = unit.id;
                    opt.textContent = `${unit.blok} / ${unit.no_unit} (${unit.tipe}, ${unit.lt_lb})`;
                    Object.keys(unit).forEach(key => { opt.dataset[key] = unit[key]; });
                    unitSelection.appendChild(opt);
                });
            }
            
            unitSelection.disabled = units.length === 0;
            nextStepBtn.disabled = true;

        } catch (error) {
            showNotification('Gagal memuat data unit: ' + error.message, 'error');
            console.error("Fetch and Render Error:", error);
        }
    };

    // --- EVENT LISTENERS ---
    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentParticipantId = button.dataset.participantId;
            currentParticipantName = button.dataset.participantName;
            modalParticipantName.textContent = currentParticipantName;
            activeFilters = { tipes: [], statuses: [], bloks: [] };
            fetchAndRender();
            unitModal.classList.remove('hidden');
        });
    });

    const closeModal = () => unitModal.classList.add('hidden');
    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    unitSelection.addEventListener('change', () => {
        nextStepBtn.disabled = unitSelection.value === '';
        if (unitSelection.value) {
            const selectedOption = unitSelection.options[unitSelection.selectedIndex];
            selectedUnitData = selectedOption.dataset;
        } else {
            selectedUnitData = null;
        }
    });

    nextStepBtn.addEventListener('click', () => {
        if (!selectedUnitData) return;
        confirmDetails.innerHTML = `
            <p class="mb-2">Anda akan memilih unit untuk <strong>${currentParticipantName}</strong>.</p>
            <div class="bg-gray-100 p-4 rounded-lg text-left">
                <p><strong>Tipe:</strong> ${selectedUnitData.tipe}</p>
                <p><strong>Unit:</strong> ${selectedUnitData.blok} / ${selectedUnitData.no_unit}</p>
                <p><strong>LT/LB:</strong> ${selectedUnitData.lt_lb}</p>
                <p><strong>Status Awal:</strong> ${selectedUnitData.status_unit}</p>
            </div>`;
        unitModal.classList.add('hidden');
        confirmModal.classList.remove('hidden');
    });

    backToSelectBtn.addEventListener('click', () => {
        confirmModal.classList.add('hidden');
        unitModal.classList.remove('hidden');
    });

    confirmBtn.addEventListener('click', async () => {
        const formData = new FormData();
        formData.append('participant_id', currentParticipantId);
        formData.append('unit_id', selectedUnitData.id);

        try {
            const response = await fetch('api/assign_unit.php', { method: 'POST', body: formData });
            const result = await response.json();
            
            if (result.success) {
                showNotification(result.message, 'success');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showNotification('Terjadi kesalahan: ' + error.message, 'error');
        } finally {
            confirmModal.classList.add('hidden');
        }
    });
});
</script>
