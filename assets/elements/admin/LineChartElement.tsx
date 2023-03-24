import React from 'react';
import ReactDOM from 'react-dom/client';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend,
} from 'chart.js';
import { Line } from 'react-chartjs-2';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend
);

export class LineChartElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element.parentElement);
        const points = JSON.parse(element.getAttribute('points'));
        const xKey = element.getAttribute('x') || 'x';
        const yKey = element.getAttribute('y') || 'y'

        const data = {
            labels: points.map((point) => {
                const date = new Date();
                date.setMonth(point[xKey] - 1);
                return date.toLocaleString(undefined, { month: 'long' });
            }),
            datasets: [
                {
                    fill: true,
                    data: points.map((point) => point[yKey]),
                    backgroundColor: '#4869ee0C',
                    borderColor: '#4869ee',
                    borderWidth: 2,
                },
            ],
        };

        const options = {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            },
        };

        root.render(
            <Line options={options} data={data} />,
        );
    }
}