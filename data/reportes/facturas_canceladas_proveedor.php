<?php
    require('../../fpdf/fpdf.php');
    include '../../procesos/base.php';
    include '../../procesos/funciones.php';
    conectarse();    
    date_default_timezone_set('America/Lima'); 
    session_start()   ;
    class PDF extends FPDF{   
        var $widths;
        var $aligns;       
        function SetWidths($w){            
            $this->widths=$w;
        }                       
        function Header(){                         
            $this->AddFont('Amble-Regular','','Amble-Regular.php');
            $this->SetFont('Amble-Regular','',10);        
            $fecha = date('Y-m-d', time());
            $this->SetX(1);
            $this->SetY(1);
            $this->Cell(20, 5, $fecha, 0,0, 'C', 0);                         
            $this->Cell(150, 5, "CLIENTE", 0,1, 'R', 0);      
            $this->SetFont('Arial','B',16);                                                    
            $this->Cell(190, 8, "EMPRESA: ".$_SESSION['empresa'], 0,1, 'C',0);                                
            $this->Image('../../images/logo_empresa.jpg',1,8,40,30);
            $this->SetFont('Amble-Regular','',10);        
            $this->Cell(180, 5, "PROPIETARIO: ".utf8_decode($_SESSION['propietario']),0,1, 'C',0);                                
            $this->Cell(70, 5, "TEL.: ".utf8_decode($_SESSION['telefono']),0,0, 'R',0);                                
            $this->Cell(60, 5, "CEL.: ".utf8_decode($_SESSION['celular']),0,1, 'C',0);                                
            $this->Cell(170, 5, "DIR.: ".utf8_decode($_SESSION['direccion']),0,1, 'C',0);                                
            $this->Cell(170, 5, "SLOGAN.: ".utf8_decode($_SESSION['slogan']),0,1, 'C',0);                                
            $this->Cell(170, 5, utf8_decode( $_SESSION['pais_ciudad']),0,1, 'C',0);                                                                                                    
            $this->SetDrawColor(0,0,0);
            $this->SetLineWidth(0.4);            
            $this->Line(1,45,210,45);            
            $this->SetFont('Arial','B',12);                                                                            
            $this->Cell(190, 5, utf8_decode("FACTURAS CANCELADAS POR PROVEEDOR"),0,1, 'C',0);                                                                                                                            
            $this->SetFont('Amble-Regular','',10);        
            $this->Ln(3);
            $this->SetFillColor(255,255,225);            
            $this->SetLineWidth(0.2);                                        
        }
        function Footer(){            
            $this->SetY(-15);            
            $this->SetFont('Arial','I',8);            
            $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'C');
        }               
    }
    $pdf = new PDF('P','mm','a4');
    $pdf->AddPage();
    $pdf->SetMargins(0,0,0,0);
    $pdf->AliasNbPages();
    $pdf->AddFont('Amble-Regular');                    
    $pdf->SetFont('Amble-Regular','',10);       
    $pdf->SetFont('Arial','B',9);   
    $pdf->SetX(5);    
    $pdf->SetFont('Amble-Regular','',9); 
    
    $total=0;
    $sub=0;
    $desc=0;
    $ivaT=0;
    $repetido=0;    
    
    $consulta=pg_query('select * FROM proveedores order by id_proveedor asc');
    while($row=pg_fetch_row($consulta)){
        $repetido=0;
        $sub=0;
        $sql1=pg_query("select * from factura_compra where estado='Activo' and id_proveedor='$row[0]' order by forma_pago asc");
        if(pg_num_rows($sql1)){
            while($row1=pg_fetch_row($sql1)){
                if($row1[14]=='Contado'){
                    if($repetido==0){
                        $pdf->SetX(1); 
                        $pdf->SetFillColor(187, 179, 180);            
                        $pdf->Cell(70, 6, maxCaracter(utf8_decode('RUC/DNI:'.$row[2]),35),1,0, 'L',1);                                     
                        $pdf->Cell(135, 6, maxCaracter(utf8_decode('NOMBRES:'.$row[3]),50),1,1, 'L',1);                                                             
                        $pdf->Ln(2);   
                        $pdf->SetX(1); 
                        $pdf->Cell(25, 6, utf8_decode('Comprobante'),1,0, 'C',0);                                     
                        $pdf->Cell(30, 6, utf8_decode('Tipo Documento'),1,0, 'C',0);                                     
                        $pdf->Cell(45, 6, utf8_decode('Nro Factura'),1,0, 'C',0);                                                             
                        $pdf->Cell(25, 6, utf8_decode('Total'),1,0, 'C',0);                                     
                        $pdf->Cell(25, 6, utf8_decode('Valor Pago'),1,0, 'C',0);                                     
                        $pdf->Cell(25, 6, utf8_decode('Saldo'),1,0, 'C',0);                                     
                        $pdf->Cell(30, 6, utf8_decode('Fecha Pago'),1,1, 'C',0);
                        $repetido=1;
                        $contador=1;                        
                    }  
                    $pdf->Cell(25, 6, utf8_decode($row1[0]),0,0, 'C',0);                                     
                    $pdf->Cell(30, 6, utf8_decode($row1[10]),0,0, 'C',0);                                     
                    $pdf->Cell(45, 6, substr($row1[11],8,30),0,0, 'C',0);                                         
                    $pdf->Cell(25, 6, utf8_decode($row1[19]),0,0, 'C',0);                                         
                    $pdf->Cell(25, 6, utf8_decode($row1[19]),0,0, 'C',0);                                     
                    $pdf->Cell(25, 6, utf8_decode('0.00'),0,0, 'C',0);                                         
                    $pdf->Cell(30, 6, utf8_decode($row1[5]),0,1, 'C',0);                    
                    $repetido=1;   
                    $sub=$sub+$row1[19];                                        
                } 
                else{    
                    if($repetido==0){ 
                        $pdf->SetX(1); 
                        $pdf->SetFillColor(187, 179, 180);            
                        $pdf->Cell(70, 6, maxCaracter(utf8_decode('RUC/DNI:'.$row[2]),35),1,0, 'L',1);                                     
                        $pdf->Cell(135, 6, maxCaracter(utf8_decode('NOMBRES:'.$row[3]),50),1,1, 'L',1);                                                             
                        $pdf->Ln(2);   
                        $pdf->SetX(1); 
                        $pdf->Cell(25, 6, utf8_decode('Comprobante'),1,0, 'C',0);                                     
                        $pdf->Cell(30, 6, utf8_decode('Tipo Documento'),1,0, 'C',0);                                     
                        $pdf->Cell(45, 6, utf8_decode('Nro Factura'),1,0, 'C',0);                                                             
                        $pdf->Cell(25, 6, utf8_decode('Total'),1,0, 'C',0);                                     
                        $pdf->Cell(25, 6, utf8_decode('Valor Pago'),1,0, 'C',0);                                     
                        $pdf->Cell(25, 6, utf8_decode('Saldo'),1,0, 'C',0);                                     
                        $pdf->Cell(30, 6, utf8_decode('Fecha Pago'),1,1, 'C',0);                                               
                        $repetido=1;
                        $contador=1;                        
                    }                  
                    $sql2=pg_query("select * from factura_compra,pagos_compra where factura_compra.id_factura_compra= pagos_compra.id_factura_compra and pagos_compra.estado='Cancelado' and pagos_compra.id_proveedor='$row[0]' and factura_compra.id_factura_compra='$row1[0]'");
                    while($row2=pg_fetch_row($sql2)){
                        $pdf->Cell(25, 6, utf8_decode($row2[0]),0,0, 'C',0);                                     
                        $pdf->Cell(30, 6, utf8_decode($row2[10]),0,0, 'C',0);                                     
                        $pdf->Cell(45, 6, substr($row2[11],8,30),0,0, 'C',0);                                         
                        $pdf->Cell(25, 6, utf8_decode($row2[19]),0,0, 'C',0);                                         
                        $pdf->Cell(25, 6, utf8_decode($row2[19]),0,0, 'C',0);                                     
                        $pdf->Cell(25, 6, utf8_decode('0.00'),0,0, 'C',0);                                         
                        $pdf->Cell(30, 6, utf8_decode($row2[25]),0,1, 'C',0);                    
                        $sub=$sub+$row2[19];                        
                    }
                }
            }
            if($contador>0){
                $pdf->SetX(1);                                             
                $pdf->Cell(207, 0, utf8_decode(""),1,1, 'R',0);
                $pdf->Cell(127, 6, utf8_decode("Totales"),0,0, 'R',0);
                $pdf->Cell(24, 6, maxCaracter((number_format($sub,2,',','.')),20),0,1, 'C',0);  
                $pdf->Ln(3);                                               
            }
        }
    }
    $pdf->Output();
?>