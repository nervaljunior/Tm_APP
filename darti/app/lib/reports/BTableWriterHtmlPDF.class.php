<?php
/**
 * PDF Table writer (dompdf)
 *
 */
class BTableWriterHtmlPDF extends TTableWriterHTML
{
    private $pdf;
    private $orientation;
    private $format;
    
    public function __construct($widths, $orientation='P', $format = 'A4')
    {
        parent::__construct($widths);
        $this->orientation = $orientation == 'P' ? 'portrait' : 'landscape';
        $this->format = $format;
        
        $this->pdf = new AdiantiHTMLDocumentParser();
    }
    
    /**
     * Returns the native writer
     */
    public function getNativeWriter()
    {
        return $this->pdf;
    }
    /**
     * Save the current file
     * @param $filename file name
     */
    public function save($filename)
    {
        if (is_callable($this->footerCallback))
        {
            call_user_func($this->footerCallback, $this);
        }
        
        ob_start();
        echo "<html>\n";
        echo "<style>\n";
        // insere os estilos no documento
        foreach ($this->styles as $style)
        {
            $style->show();
        }
        echo "</style>\n";
        // inclui a tabela no documento
        $this->table->show();
        echo "</html>";
        $content = ob_get_clean();

        $this->pdf->addContent($content);
        $this->pdf->saveAsPDF($filename, $this->format, $this->orientation);
        return TRUE;
    }
}
