import './bootstrap';

import Chart from 'chart.js/auto';
import Swal from 'sweetalert2';

window.Swal = Swal;

// Making it available globally so inline Blade scripts can use it
window.Chart = Chart;
