<?php

require('vendor/tecnick.com/tcpdf/tcpdf.php');

class SOPDF extends TCPDF
{
    private $header;
    private $footer;

    public function setHTMLHeader($header) {
        $this->header = $header;
    }

    public function setHTMLFooter($footer) {
        $this->footer = $footer;
    }

    public function Header() {
        $this->SetFont('msungstdlight', '', 8);
        $this->writeHTML($this->header, true, false, true, false, '');
    }

    public function Footer() {
        $this->SetFont('msungstdlight', '', 8);
        $this->writeHTML($this->footer, true, false, true, false, '');
    }
}

class DEPDF extends TCPDF
{
    private $header;
    private $footer;

    public function setHTMLHeader($header) {
        $this->header = $header;
    }

    public function setHTMLFooter($footer) {
        $this->footer = $footer;
    }

    public function Header() {
        $this->SetFont('msungstdlight', '', 8);
        $this->writeHTML($this->header, true, false, true, false, '');
    }

    public function Footer() {
        $this->SetFont('msungstdlight', '', 8);
        $this->writeHTML($this->footer, true, false, true, false, '');
    }
}


class TXPDF extends TCPDF
{
	private $header;
	private $footer;
	
    public function setHTMLHeader($header)
    {
        $this->header = $header;
    }

    public function setHTMLFooter($footer)
    {
        $this->footer = $footer;
    }
	
    public function Header()
    {
        $this->SetFont('msungstdlight', '', 11);
        $this->writeHTML($this->header, true, false, true, false, '');
    }
	
	public function Footer() {
		$this->SetFont('helvetica', 'N', 8);
		$this->writeHTML($this->footer, true, false, true, false, '');
		$this->SetY(-12);
		$this->Cell(0, 10, 'Page: '. $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

class POPDF extends TCPDF
{
    private $header;
    private $footer;
    private $last_page_footer;
    protected $is_last_page = false;

    public function setHTMLHeader($header)
    {
        $this->header = $header;
    }

    public function setHTMLFooter($footer)
    {
        $this->footer = $footer;
    }

    public function setHTMLLastPageFooter($last_page_footer)
    {
        $this->last_page_footer = $last_page_footer;
    }

    public function Header()
    {
        $this->SetFont('msungstdlight', '', 11);
        $this->writeHTML($this->header, true, false, true, false, '');
    }

    public function Footer()
    {
        $this->SetFont('msungstdlight', '', 8);
        if ($this->is_last_page) {
            $this->writeHTML($this->last_page_footer, true, false, true, false, '');
        } else {
            $this->writeHTML($this->footer, true, false, true, false, '');
        }
    }

    public function lastPage($resetmargins = false) {
        $this->setPage($this->getNumPages(), $resetmargins);
        $this->is_last_page = true;
    }
}

class QUPDF extends TCPDF
{
    private $header;
    private $footer;
    private $last_page_footer;
    protected $is_last_page = false;

    public function setHTMLHeader($header)
    {
        $this->header = $header;
    }

    public function setHTMLFooter($footer)
    {
        $this->footer = $footer;
    }

    public function setHTMLLastPageFooter($last_page_footer)
    {
        $this->last_page_footer = $last_page_footer;
    }

    public function Header()
    {
        $this->SetFont('msungstdlight', '', 11);
        $this->writeHTML($this->header, true, false, true, false, '');
    }

    public function Footer()
    {
        $this->SetFont('msungstdlight', '', 8);

        if ($this->is_last_page) {
            $this->writeHTML($this->last_page_footer, true, false, true, false, '');
        } else {
            $this->writeHTML($this->footer, true, false, true, false, '');
        }
        $this->SetFont('helvetica', '', 8);
        $this->SetY(-12);
        $this->Cell(0, 10, 'Page: '. $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function lastPage($resetmargins = false) {
        $this->setPage($this->getNumPages(), $resetmargins);
        $this->is_last_page = true;
    }
}