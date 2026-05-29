// Philippines Address Data - Regions, Provinces, Cities, Barangays
const philippinesAddresses = {
  regions: [
    { id: 'ncr', name: 'NCR (National Capital Region)' },
    { id: 'r1', name: 'Region 1 (Ilocos Region)' },
    { id: 'r2', name: 'Region 2 (Cagayan Valley)' },
    { id: 'r3', name: 'Region 3 (Calabarzon)' },
    { id: 'r4a', name: 'Region 4-A (MIMAROPA)' },
    { id: 'r5', name: 'Region 5 (Bicol Region)' },
    { id: 'r6', name: 'Region 6 (Western Visayas)' },
    { id: 'r7', name: 'Region 7 (Central Visayas)' },
    { id: 'r8', name: 'Region 8 (Eastern Visayas)' },
    { id: 'r9', name: 'Region 9 (Zamboanga Peninsula)' },
    { id: 'r10', name: 'Region 10 (Northern Mindanao)' },
    { id: 'r11', name: 'Region 11 (Davao Region)' },
    { id: 'r12', name: 'Region 12 (Soccsksargen)' },
    { id: 'caraga', name: 'Caraga Region' },
    { id: 'barmm', name: 'BARMM (Bangsamoro)' }
  ],
  provinces: {
    ncr: [
      { id: 'ncr_manila', name: 'Manila' },
      { id: 'ncr_quezon', name: 'Quezon City' },
      { id: 'ncr_caloocan', name: 'Caloocan' },
      { id: 'ncr_marikina', name: 'Marikina' },
      { id: 'ncr_pasig', name: 'Pasig' },
      { id: 'ncr_rizal', name: 'Rizal' },
      { id: 'ncr_laguna', name: 'Laguna' },
      { id: 'ncr_cavite', name: 'Cavite' },
      { id: 'ncr_bulacan', name: 'Bulacan' }
    ],
    r1: [
      { id: 'r1_ilocos_norte', name: 'Ilocos Norte' },
      { id: 'r1_ilocos_sur', name: 'Ilocos Sur' },
      { id: 'r1_la_union', name: 'La Union' },
      { id: 'r1_pangasinan', name: 'Pangasinan' }
    ],
    r3: [
      { id: 'r3_batangas', name: 'Batangas' },
      { id: 'r3_cavite', name: 'Cavite' },
      { id: 'r3_laguna', name: 'Laguna' },
      { id: 'r3_quezon', name: 'Quezon' },
      { id: 'r3_rizal', name: 'Rizal' }
    ],
    r6: [
      { id: 'r6_aklan', name: 'Aklan' },
      { id: 'r6_antique', name: 'Antique' },
      { id: 'r6_capiz', name: 'Capiz' },
      { id: 'r6_iloilo', name: 'Iloilo' },
      { id: 'r6_negros_occidental', name: 'Negros Occidental' }
    ],
    r7: [
      { id: 'r7_bohol', name: 'Bohol' },
      { id: 'r7_cebu', name: 'Cebu' },
      { id: 'r7_negros_oriental', name: 'Negros Oriental' },
      { id: 'r7_siquijor', name: 'Siquijor' }
    ],
    r11: [
      { id: 'r11_davao_del_norte', name: 'Davao del Norte' },
      { id: 'r11_davao_del_sur', name: 'Davao del Sur' },
      { id: 'r11_davao_oriental', name: 'Davao Oriental' },
      { id: 'r11_davao_de_oro', name: 'Davao de Oro' }
    ]
  },
  cities: {
    ncr_manila: [
      { id: 'manila_binondo', name: 'Binondo' },
      { id: 'manila_intramuros', name: 'Intramuros' },
      { id: 'manila_malate', name: 'Malate' },
      { id: 'manila_paco', name: 'Paco' },
      { id: 'manila_quiapo', name: 'Quiapo' }
    ],
    ncr_quezon: [
      { id: 'qc_ba0', name: 'Barangay Commonwealth' },
      { id: 'qc_ba1', name: 'Barangay Cubao' },
      { id: 'qc_ba2', name: 'Barangay Diliman' },
      { id: 'qc_ba3', name: 'Barangay East Triangle' },
      { id: 'qc_ba4', name: 'Barangay New Manila' }
    ],
    r3_cavite: [
      { id: 'cavite_bacoor', name: 'Bacoor' },
      { id: 'cavite_kawit', name: 'Kawit' },
      { id: 'cavite_rosario', name: 'Rosario' },
      { id: 'cavite_dasmariñas', name: 'Dasmariñas' },
      { id: 'cavite_silang', name: 'Silang' }
    ],
    r3_laguna: [
      { id: 'laguna_binan', name: 'Biñan' },
      { id: 'laguna_cabuyao', name: 'Cabuyao' },
      { id: 'laguna_laguna', name: 'Laguna' },
      { id: 'laguna_los_banos', name: 'Los Baños' },
      { id: 'laguna_santa_rosa', name: 'Santa Rosa' }
    ],
    r6_iloilo: [
      { id: 'iloilo_city', name: 'Iloilo City' },
      { id: 'iloilo_oton', name: 'Oton' },
      { id: 'iloilo_leganes', name: 'Leganes' },
      { id: 'iloilo_jaro', name: 'Jaro' }
    ],
    r7_cebu: [
      { id: 'cebu_city', name: 'Cebu City' },
      { id: 'cebu_mandaue', name: 'Mandaue' },
      { id: 'cebu_lapu_lapu', name: 'Lapu-Lapu' },
      { id: 'cebu_talisay', name: 'Talisay' },
      { id: 'cebu_consolacion', name: 'Consolacion' }
    ],
    r11_davao: [
      { id: 'davao_city', name: 'Davao City' },
      { id: 'davao_panabo', name: 'Panabo' },
      { id: 'davao_samal', name: 'Samal' },
      { id: 'davao_toril', name: 'Toril' }
    ]
  },
  barangays: {
    manila_binondo: ['San Nicolas', 'Binondo', 'San Agustin', 'Santa Cruz'],
    manila_malate: ['Baclaran', 'Tamis', 'Pintuhan', 'Pio del Pilar'],
    qc_diliman: ['Diliman', 'Project 8', 'Project 4', 'Kamuning', 'Cubao'],
    cavite_bacoor: ['Bacoor Proper', 'Aniban North', 'Aniban South', 'San Agustin'],
    laguna_santa_rosa: ['Aplaya', 'Balibago', 'Kanluran', 'Silangan'],
    iloilo_city: ['Cabanban', 'Dungaw', 'Kailangan', 'La Paz'],
    cebu_city: ['Apas', 'Basak San Nicolas', 'Benedicto', 'Busay'],
    davao_city: ['Agdao', 'Bucana', 'Calinan', 'Gumalang']
  }
};

// Initialize address dropdowns
function initAddressSelects() {
  const regionSelect = document.getElementById('orderRegion');
  const provinceSelect = document.getElementById('orderProvince');
  const citySelect = document.getElementById('orderCity');
  const barangaySelect = document.getElementById('orderBarangay');

  if (!regionSelect) return;

  // Populate regions
  philippinesAddresses.regions.forEach(region => {
    const opt = document.createElement('option');
    opt.value = region.id;
    opt.textContent = region.name;
    regionSelect.appendChild(opt);
  });

  // Region change handler
  regionSelect.addEventListener('change', (e) => {
    const regionId = e.target.value;
    provinceSelect.innerHTML = '<option value="">Select Province</option>';
    citySelect.innerHTML = '<option value="">Select City</option>';
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (regionId && philippinesAddresses.provinces[regionId]) {
      philippinesAddresses.provinces[regionId].forEach(province => {
        const opt = document.createElement('option');
        opt.value = province.id;
        opt.textContent = province.name;
        provinceSelect.appendChild(opt);
      });
    }
  });

  // Province change handler
  provinceSelect.addEventListener('change', (e) => {
    const provinceId = e.target.value;
    citySelect.innerHTML = '<option value="">Select City</option>';
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (provinceId && philippinesAddresses.cities[provinceId]) {
      philippinesAddresses.cities[provinceId].forEach(city => {
        const opt = document.createElement('option');
        opt.value = city.id;
        opt.textContent = city.name;
        citySelect.appendChild(opt);
      });
    }
  });

  // City change handler
  citySelect.addEventListener('change', (e) => {
    const cityId = e.target.value;
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (cityId && philippinesAddresses.barangays[cityId]) {
      philippinesAddresses.barangays[cityId].forEach(barangay => {
        const opt = document.createElement('option');
        opt.value = barangay;
        opt.textContent = barangay;
        barangaySelect.appendChild(opt);
      });
    }
  });
}

// Get formatted address
function getFormattedAddress() {
  const region = document.getElementById('orderRegion')?.options[document.getElementById('orderRegion')?.selectedIndex]?.text || '';
  const province = document.getElementById('orderProvince')?.options[document.getElementById('orderProvince')?.selectedIndex]?.text || '';
  const city = document.getElementById('orderCity')?.options[document.getElementById('orderCity')?.selectedIndex]?.text || '';
  const barangay = document.getElementById('orderBarangay')?.value || '';
  
  return [barangay, city, province, region].filter(Boolean).join(', ');
}
