<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Status Distribution --}}
        <div class="bg-white border border-black/[0.07] rounded-xl p-5">
            <h3 class="text-[13px] font-medium text-gray-900 mb-4">Task Status</h3>
            <div class="h-[200px] relative"
                x-data="statusChart({{ json_encode($this->statusData) }})"
                x-on:refresh-status-chart.window="refresh($event.detail)"
                wire:ignore>
                <canvas x-ref="canvas" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Priority Distribution --}}
        <div class="bg-white border border-black/[0.07] rounded-xl p-5">
            <h3 class="text-[13px] font-medium text-gray-900 mb-4">Tasks by Priority</h3>
            <div class="h-[200px] relative"
                x-data="priorityChart({{ json_encode($this->priorityData) }})"
                wire:ignore>
                <canvas x-ref="canvas" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('statusChart', (data) => ({
        chart: null,
        init(){
            this.chart = new Chart(this.$refs.canvas, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: ['#378ADD', '#7F77DD', '#1D9E75', '#EF9F27'],
                        borderWidth: 0,
                        hoverOffset: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        },
        refresh(newData) {

            this.chart.data.labels = newData.labels;
            this.chart.data.datasets[0].data = newData.data;
            this.chart.update();
        }
    }));

    Alpine.data('priorityChart', (data) => ({
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.canvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: ['#D1D5DB', '#9CA3AF', '#6B7280', '#374151', '#1F2937'],
                        borderRadius: 6,
                        barThickness: 24,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 10 } },
                            grid: { color: '#F3F4F6' }
                        }
                    }
                }
            });
        }
    }));
</script>
@endscript
