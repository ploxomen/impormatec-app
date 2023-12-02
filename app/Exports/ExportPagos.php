<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportPagos implements FromView,ShouldAutoSize,WithStyles
{
    private $datos;
    private $vista;
    private $fechaInicio;
    private $fechaFin;
    private $filaInicial = 5;
    private $filaFinal = 5;
    function __construct($datos,$fechaInicio,$fechaFin,$vista){
        $this->datos = $datos;
        $this->vista = $vista;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }
    public function view(): View
    {
        return view($this->vista, [
            'ordenesServicios' => $this->datos,
            'fechaInicioReporte' => $this->fechaInicio,
            'fechaFinReporte' => $this->fechaFin
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        foreach ($this->datos as $ordenServicio) {
            $this->filaFinal += $ordenServicio->pagoCuotas()->count() === 0 ? 1 : $ordenServicio->pagoCuotas()->count();            
        }
        $rango = "A" . $this->filaInicial . ":L" . $this->filaFinal;
        $titulo = $sheet->getStyle('B2');
        $titulo->getFont()->setBold(true);
        $titulo->getFont()->setUnderline(true);
        $titulo->getFont()->setSize(22);
        $titulo->getAlignment()->setHorizontal('center');
        $cabeceraTabla = $sheet->getStyle("A".$this->filaInicial . ':' . "L" . $this->filaInicial);
        $cabeceraTabla->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E5E5E5', // Color plomo (puedes cambiar esto)
                ],
            ],
        ]);
        $cabeceraTabla->getFont()->setBold(true);
        $cabeceraTabla->getAlignment()->setHorizontal('center');
        $sheet->getRowDimension(1)->setRowHeight(15);
        $sheet->getRowDimension($this->filaInicial)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
    }
}
