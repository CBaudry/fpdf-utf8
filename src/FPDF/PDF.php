<?php

namespace cbaudry\FpdfBundle;

use cbaudry\FpdfBundle\Exception\FPDFException;
use cbaudry\FpdfBundle\PDFBarcode;
use cbaudry\FpdfBundle\PDFProtection;

/**
 * Class PDF
 * @package csFPDF
 */
class PDF {

    const ORIENTATION_PORTRAIT = 'P';
    const ORIENTATION_LANDSCAPE = 'L';

    const DOCUMENT_STATE_NOT_INITIALIZED = 0;
    const DOCUMENT_STATE_INITIALIZED = 1;
    const DOCUMENT_STATE_CREATING = 2;
    const DOCUMENT_STATE_TERMINATED = 3;

    const FONT_TRUETYPE = 'TTF';

    const FILE_FONT_METRICS = 'fm-';
    const FILE_CHARACTER_WIDTH = 'cw-';

    /**
     * Version number
     *
     * @var string
     */
    protected $version = '3.0';

    /**
     * PDS's unique ID
     *
     * @var string
     */
    protected string $str_id = '';

    /**
     * @var bool
     */
    protected $bol_uniform_subset = false;

    /**
     * Current page number
     *
     * @var int
     */
    protected $int_page = 0;

    /**
     * Current object number
     *
     * @var int
     */
    protected $int_current_object = 2;

    /**
     * Array of object offsets
     *
     * @var array
     */
    protected $arr_offsets = [];

    /**
     * Buffer holding in-memory PDF
     *
     * @var string
     */
    protected $str_buffer = '';

    /**
     * Array containing pages
     *
     * @var array
     */
    protected $arr_pages = [];

    /**
     * Current document state
     *
     * @var int
     */
    protected $int_state = self::DOCUMENT_STATE_NOT_INITIALIZED;

    /**
     * Compression flag
     *
     * @var bool
     */
    protected $bol_compress = false;

    /**
     * Scale factor (number of points in user unit)
     *
     * @var int
     */
    protected $flt_scale_factor = 1;

    /**
     * Default orientation
     *
     * @var string
     */
    protected $str_default_orientation = '';

    /**
     * Current orientation
     *
     * @var string
     */
    protected $str_current_orientation = '';

    /**
     * Standard page sizes
     *
     * @var array
     */
    protected $arr_standard_page_sizes = [
        'a3' => array(841.89, 1190.55),
        'a4' => array(595.28, 841.89),
        'a5' => array(420.94, 595.28),
        'letter' => array(612, 792),
        'legal' => array(612, 1008)
    ];

    /**
     * Default page size
     *
     * @var array|string
     */
    protected $arr_default_page_sizes = [];

    /**
     * Current page size
     *
     * @var array|string
     */
    protected $arr_current_page_sizes = [];

    /**
     * Used for pages with non default sizes or orientations
     *
     * @var array
     */
    protected $arr_page_sizes = [];

    /**
     * Dimensions of current page in points
     *
     * @var float
     */
    protected $flt_width_points, $flt_height_points = 0.00;

    /**
     * Dimensions of current page in user units
     *
     * @var float
     */
    protected $flt_current_width, $flt_current_height = 0.00;

    /**
     * Left Margin
     *
     * @var int
     */
    protected $int_left_margin = 0;

    /**
     * Top Margin
     *
     * @var int
     */
    protected $int_top_margin = 0;

    /**
     * Right Margin
     *
     * @var int
     */
    protected $int_right_margin = 0;

    /**
     * Page break margin
     *
     * @var int
     */
    protected $int_break_margin = 0;

    /**
     * Cell Margin
     *
     * @var float|int
     */
    protected $int_cell_margin = 0;

    /**
     * Current position in user unit
     *
     * @var
     */
    protected $flt_position_x, $flt_position_y = 0.00;

    /**
     * Height of last printed cell
     *
     * @var float
     */
    protected $flt_last_cell_height = 0.00;

    /**
     * Line width in user units
     *
     * @var float
     */
    protected $flt_line_width = 0.00;

    /**
     * The path containing fonts
     *
     * @var string
     */
    protected $str_font_path = '';

    /**
     * Array of core font names
     *
     * @var array
     */
    protected $arr_core_fonts = [
        'courier',
        'helvetica',
        'times',
        'symbol',
        'zapfdingbats'
    ];

    /**
     * Array of used fonts
     *
     * @var array
     */
    protected $arr_fonts = [];

    /**
     * Array of font files
     *
     * @var array
     */
    protected $arr_font_files = [];

    /**
     * Array of encoding differences
     *
     * @var array
     */
    protected $arr_encoding_diffs = [];

    /**
     * Current font family
     *
     * @var string
     */
    protected $str_current_font_family = '';

    /**
     * Current font style
     *
     * @var string
     */
    protected $str_current_font_style = '';

    /**
     * Underline flag
     *
     * @var bool
     */
    protected $bol_underline = false;

    /**
     * Array of current font info
     *
     * @var array
     */
    protected $arr_current_font_info = [];

    /**
     * Current font size in points
     *
     * @var int
     */
    protected $int_current_font_size = 12;

    /**
     * Current font size in user units
     *
     * @var int
     */
    protected $int_font_size_user = 0;

    /**
     * The draw color
     *
     * @var string
     */
    protected $str_draw_color = '0 G';

    /**
     * The fill color
     *
     * @var string
     */
    protected $str_fill_color = '0 g';

    /**
     * The text color
     *
     * @var string
     */
    protected $str_text_color = '0 g';

    /**
     * Indicates whether fill and text colors are different
     *
     * @var bool
     */
    protected $bol_fill_text_differ = false;

    /**
     * The word spacing
     *
     * @var int
     */
    protected $int_word_spacing = 0;

    /**
     * Array of used images
     *
     * @var array
     */
    protected $arr_images = [];

    /**
     * Array of links in pages
     *
     * @var array
     */
    protected $arr_page_links = [];

    /**
     * Array of internal links
     *
     * @var array
     */
    protected $arr_internal_links = [];

    /**
     * Automatic page breaking
     *
     * @var bool
     */
    protected $bol_auto_page_break = false;

    /**
     * Threshold used to trigger page breaks
     *
     * @var float
     */
    protected $flt_page_break_trigger = 0.00;

    /**
     * Flag set when processing header
     *
     * @var bool
     */
    protected $bol_in_header = false;

    /**
     * Flag set when processing footer
     *
     * @var bool
     */
    protected $bol_in_footer = false;

    /**
     * Zoom display mode
     *
     * @var mixed
     */
    protected $mix_zoom_mode;

    /**
     * Layout display mode
     *
     * @var string
     */
    protected $str_layout_mode = '';

    /**
     * The title
     *
     * @var string
     */
    protected $str_title = '';

    /**
     * The subject
     *
     * @var string
     */
    protected $str_subject = '';

    /**
     * The author
     *
     * @var string
     */
    protected $str_author = '';

    /**
     * The keywords
     *
     * @var string
     */
    protected $str_keywords = '';

    /**
     * The creator
     *
     * @var string
     */
    protected $str_creator = '';

    /**
     * The alias for total number of pages
     *
     * @var string
     */
    protected $str_alias_number_pages = '';

    /**
     * The PDF version number
     *
     * @var string
     */
    protected $str_pdf_version = '1.6';

    /**
     * The font metric cache directory, if null no cache will be used
     *
     * @var string
     */
    protected $cachePath = null;
	
    /**
     * Array of file attachments
     *
     * @var array
     */
	protected array $file_list = [];
	
	/**
	 * Files object number
	 * 
	 * @var int
	 */
    protected int $file_index;
	
	/**
	 * Ask the PDF viewer to automatically open the file attachment pane
	 * 
	 * @var bool
	 */
    protected bool $file_open_attachment_pane = false;

	/**
	 * Path to the color profile
	 * 
	 * @var ?string
	 */
	protected ?string $str_color_profile_path = null;
	
	/**
	 * Color profile object number
	 * 
	 * @var int
	 */
	protected int $int_color_profile;
	
	/**
	 * Color profile's description object number
	 * 
	 * @var int
	 */
	protected int $int_color_profile_desc;
	
	/**
	 * Microtime of the moment the "EndDoc" function is called
	 * 
	 * @var int
	 */
    protected int $int_creation_date;
	
	/**
	 * 
	 * @var array
	 */
	protected array $metadata_list = [];
	
	/**
	 * Metadata object number
	 * 
	 * @var int
	 */
    protected int $int_metadata;
	
	/**
	 * String of the XMP file
	 * 
	 * @var string|null
	 */
    protected ?string $str_xmp = null;
	
	/**
	 * Used to prevent pushing XMP data twice
	 * 
	 * @var bool
	 */
	protected bool $b_xmp_out = false;
	
	/**
	 * Instance of the PDFBarcode class, use this to add different kind of codes
	 * 
	 * @var PDFBarcode
	 */
	public ?PDFBarcode $Barcode;
	
	/**
	 * Instance of the PDFProtection class, use this to add different kind of codes
	 * 
	 * @var PDFProtection
	 */
	public ?PDFProtection $Protection;

    /**
     * PDF constructor.
     * @param string $str_orientation
     * @param string $str_units
     * @param string $str_size
     */
    public function __construct($str_orientation = 'P', $str_units = 'mm', $str_size = 'A4') {
		$this->str_id = uniqid();
		
        $this->setFontPath(__DIR__ . '/font/unifont/');
        $this->setColorProfilePath(__DIR__ . '/color_profile/sRGB2014.icc');

        // Scale factor
        switch ($str_units) {
            case 'pt':
                $this->flt_scale_factor = 1;
                break;
            case 'mm':
            case 'cm':
                $this->flt_scale_factor = 72 / 25.4;
                break;
            case 'in':
                $this->flt_scale_factor = 72;
                break;
            default:
                throw new FPDFException('Invalid unit specified: ' . $str_units, FPDFException::INVALID_UNIT);
        }

        $str_size = $this->getPageSize($str_size);
        $this->arr_default_page_sizes = $str_size;
        $this->arr_current_page_sizes = $str_size;
        // Page orientation

        switch ($str_orientation) {

            case 'portrait':
            case self::ORIENTATION_PORTRAIT:
                $this->str_default_orientation = 'P';
                $this->flt_current_width = $str_size[0];
                $this->flt_current_height = $str_size[1];
                break;

            case 'landscape':
            case self::ORIENTATION_LANDSCAPE:
                $this->str_default_orientation = 'L';
                $this->flt_current_width = $str_size[1];
                $this->flt_current_height = $str_size[0];
                break;

            default:
                throw new FPDFException('Invalid orientation: ' . $str_orientation, FPDFException::INVALID_ORIENTATION);
                break;
        }

        $this->str_current_orientation = $this->str_default_orientation;
        $this->flt_width_points = $this->flt_current_width * $this->flt_scale_factor;
        $this->flt_height_points = $this->flt_current_height * $this->flt_scale_factor;

    // Page margins (1 cm)
        $flt_margin = 28.35 / $this->flt_scale_factor;
        $this->setMargins($flt_margin, $flt_margin);

    // Interior cell margin (1 mm)
        $this->int_cell_margin = $flt_margin / 10;

    // Line width (0.2 mm)
        $this->flt_line_width = .567 / $this->flt_scale_factor;

    // Automatic page break
        $this->SetAutoPageBreak(true, 2 * $flt_margin);

    // Default display mode
        $this->SetDisplayMode('default');

    // Enable compression
        $this->SetCompression(true);

    // Used both as metadata and XMP data
		$this->metadata_list["Producer"] = 'csFPDF ' . $this->version;
		
		$this->Barcode = new PDFBarcode($this);
		$this->Protection = new PDFProtection($this);
		
    }

    /**
     * @param $int_left
     * @param $int_top
     * @param null $int_right
     */
    public function setMargins($int_left, $int_top, $int_right = null)
    {
        // Set left, top and right margins
        $this->int_left_margin = $int_left;
        $this->int_top_margin = $int_top;
        if (null === $int_right) {
            $int_right = $int_left;
        }
        $this->int_right_margin = $int_right;
    }

    /**
     * @param $int_margin
     */
    public function SetLeftMargin($int_margin)
    {
        // Set left margin
        $this->int_left_margin = $int_margin;
        if ($this->int_page > 0 && $this->flt_position_x < $int_margin) {
            $this->flt_position_x = $int_margin;
        }
    }

    /**
     * @param $int_margin
     */
    public function SetTopMargin($int_margin)
    {
        // Set top margin
        $this->int_top_margin = $int_margin;
    }

    /**
     * @param $int_margin
     */
    public function SetRightMargin($int_margin)
    {
        // Set right margin
        $this->int_right_margin = $int_margin;
    }

    /**
     * @param $bol_auto
     * @param int $int_margin
     */
    public function SetAutoPageBreak($bol_auto, $int_margin = 0)
    {
        // Set auto page break mode and triggering margin
        $this->bol_auto_page_break = $bol_auto;
        $this->int_break_margin = $int_margin;
        $this->flt_page_break_trigger = $this->flt_current_height - $int_margin;
    }

    /**
     * Set display mode and zoom view
     *
     * @param string $zoomMode Set zoom mode: default, fullpage, fullwidth or real
     * @param string $layoutMode Set layout mode: default, single, continuous or two
     */
    public function SetDisplayMode(?string $zoomMode = null, ?string $layoutMode = null) {

    // Validate zoom mode
        $zoomModes = ['default', 'fullpage', 'fullwidth', 'real'];
        if($zoomMode === null) {
            $zoomMode = $zoomModes[0];
        }

        if(!in_array($zoomMode, $zoomModes)) {
            throw new FPDFException('Invalid zoom mode specified: `'.$zoomMode.'`', FPDFException::INVALID_ZOOM_MODE);
        }

    // Valid layout mode
        $layoutModes = ['default', 'single', 'continuous', 'two'];
        if($layoutMode === null) {
            $layoutMode = $layoutModes[0];
        }

        if(!in_array($layoutMode, $layoutModes)) {
            throw new FPDFException('Invalid layout mode specified: `'.$layoutMode.'`', FPDFException::INVALID_LAYOUT_MODE);
        }

    // Set zoom and layout modes
        $this->mix_zoom_mode = $zoomMode;
        $this->str_layout_mode = $layoutMode;
    }

    /**
     * @param $bol_compression
     */
    public function SetCompression($bol_compression)
    {
        // Set page compression
        if (function_exists('gzcompress')) {
            $this->bol_compress = $bol_compression;
        } else {
            $this->bol_compress = false;
        }
    }

    /**
     * @param $str_title
     * @param bool $bol_utf8
     */
    public function SetTitle($str_title, $bol_utf8 = true)
    {
        // Title of document
        if ($bol_utf8) {
            $str_title = $this->UTF8toUTF16($str_title);
        }
        $this->str_title = $str_title;
    }

    /**
     * @param $str_subject
     * @param bool $bol_utf8
     */
    public function SetSubject($str_subject, $bol_utf8 = true)
    {
        // Subject of document
        if ($bol_utf8) {
            $str_subject = $this->UTF8toUTF16($str_subject);
        }
        $this->str_subject = $str_subject;
    }

    /**
     * @param $str_author
     * @param bool $bol_utf8
     */
    public function SetAuthor($str_author, $bol_utf8 = true)
    {
        // Author of document
        if ($bol_utf8) {
            $str_author = $this->UTF8toUTF16($str_author);
        }
        $this->str_author = $str_author;
    }

    /**
     * @param $str_keywords
     * @param bool $bol_utf8
     */
    public function SetKeywords($str_keywords, $bol_utf8 = true)
    {
        // Keywords of document
        if ($bol_utf8) {
            $str_keywords = $this->UTF8toUTF16($str_keywords);
        }
        $this->str_keywords = $str_keywords;
    }

    /**
     * @param $str_creator
     * @param bool $bol_utf8
     */
    public function SetCreator($str_creator, $bol_utf8 = true)
    {
        // Creator of document
        if ($bol_utf8) {
            $str_creator = $this->UTF8toUTF16($str_creator);
        }
        $this->str_creator = $str_creator;
    }

    /**
     * @param string $str_alias
     */
    public function AliasNbPages($str_alias = '{nb}')
    {
        // Define an alias for total number of pages
        $this->str_alias_number_pages = $str_alias;
    }

    /**
     * Inits the document
     */
    public function Open()
    {
        // Begin document
        $this->int_state = self::DOCUMENT_STATE_INITIALIZED;
    }

    /**
     * Closes the document
     */
    public function Close()
    {
        // Terminate document
        if ($this->int_state == self::DOCUMENT_STATE_TERMINATED) {
            return;
        }
        if ($this->int_page == 0) {
            $this->AddPage();
        }
        // Page footer
//        $this->bol_in_footer = true;
//        $this->Footer();
//        $this->bol_in_footer = false;
        // Close page
        $this->EndPage();
        // Close document
        $this->EndDoc();
    }

    /**
     * @param string $str_orientation
     * @param string $str_size
     */
    public function AddPage($str_orientation = '', $str_size = '')
    {
        // Start a new page
        if ($this->int_state == self::DOCUMENT_STATE_NOT_INITIALIZED) {
            $this->Open();
        }
        $str_font_family = $this->str_current_font_family;
        $str_style = $this->str_current_font_style . ($this->bol_underline ? 'U' : '');
        $int_font_size = $this->int_current_font_size;
        $flt_line_width = $this->flt_line_width;
        $str_draw_color = $this->str_draw_color;
        $str_fill_color = $this->str_fill_color;
        $str_text_color = $this->str_text_color;
        $bol_fill_text_differ = $this->bol_fill_text_differ;
        if ($this->int_page > 0) {
            // Page footer
            // $this->bol_in_footer = true;
            // $this->Footer();
            // $this->bol_in_footer = false;
            // Close page
            $this->EndPage();
        }
        // Start new page
        $this->BeginPage($str_orientation, $str_size);
        // Set line cap style to square
        $this->Out('2 J');
        // Set line width
        $this->flt_line_width = $flt_line_width;
        $this->Out(sprintf('%.2F w', $flt_line_width * $this->flt_scale_factor));
        // Set font
        if ($str_font_family) {
            $this->SetFont($str_font_family, $str_style, $int_font_size);
        }
        // Set colors
        $this->str_draw_color = $str_draw_color;
        if ($str_draw_color != '0 G') {
            $this->Out($str_draw_color);
        }
        $this->str_fill_color = $str_fill_color;
        if ($str_fill_color != '0 g') {
            $this->Out($str_fill_color);
        }
        $this->str_text_color = $str_text_color;
        $this->bol_fill_text_differ = $bol_fill_text_differ;
        // Page header
        // $this->bol_in_header = true;
        // $this->Header();
        // $this->bol_in_header = false;
        // Restore line width
        if ($this->flt_line_width != $flt_line_width) {
            $this->flt_line_width = $flt_line_width;
            $this->Out(sprintf('%.2F w', $flt_line_width * $this->flt_scale_factor));
        }
        // Restore font
        if ($str_font_family) {
            $this->SetFont($str_font_family, $str_style, $int_font_size);
        }
        // Restore colors
        if ($this->str_draw_color != $str_draw_color) {
            $this->str_draw_color = $str_draw_color;
            $this->Out($str_draw_color);
        }
        if ($this->str_fill_color != $str_fill_color) {
            $this->str_fill_color = $str_fill_color;
            $this->Out($str_fill_color);
        }
        $this->str_text_color = $str_text_color;
        $this->bol_fill_text_differ = $bol_fill_text_differ;
    }

    /**
     *
     */
    public function Header()
    {
        // To be implemented in your own inherited class
    }

    /**
     *
     */
    public function Footer()
    {
        // To be implemented in your own inherited class
    }

    /**
     * @return int
     */
    public function PageNo()
    {
        // Get current page number
        return $this->int_page;
    }
	
	/**
	 * @param array $args
     * @return string
     */
	protected function getColourString($args)
    {
        if (count($args) && is_array($args[0])) {
            $args = $args[0];
        }
        switch (count($args)) {
            case 1:
                return sprintf('%.3f g', $args[0] / 255);
            case 3:
                return sprintf('%.3f %.3f %.3f rg', $args[0] / 255, $args[1] / 255, $args[2] / 255);
            case 4:
                return sprintf('%.3f %.3f %.3f %.3f k', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
            default:
                return '0 g';
        }
    }

    /**
	 * Support for use CMYK colors
	 * If you pass no parameter, you get black.
	 * If you pass 1 parameter, it is a grayscale component (between 0 and 100).
	 * If you pass 3 parameters, they are RGB components (between 0 and 255).
	 * If you pass 4 parameters, they are CMYK components (between 0 and 100).
	 * Can you pass the color values as an array:
	 * $rgb_color = [$red, $green, $blue];
	 * $cmyk_color = [$cyan, $magenta, $yellow, $key]; // In CMYK the black color is called Key.
	 * 
     * @param ?int $int_red
     * @param ?int $int_green
     * @param ?int $int_blue
     */
    public function SetDrawColor()
    {
        $args = func_get_args();
		// Setting draw colour (unlike any other colours) requires it to be uppercase
        $str_draw_color = strtoupper($this->getColourString($args));
		if($str_draw_color === $this->str_draw_color){
			return;
		}
        $this->str_draw_color = $str_draw_color;
        if ($this->int_page > 0) {
            $this->Out($this->str_draw_color);
        }
    }

    /**
	 * Support for use CMYK colors
	 * If you pass no parameter, you get black.
	 * If you pass 1 parameter, it is a grayscale component (between 0 and 100).
	 * If you pass 3 parameters, they are RGB components (between 0 and 255).
	 * If you pass 4 parameters, they are CMYK components (between 0 and 100).
	 * Can you pass the color values as an array:
	 * $rgb_color = [$red, $green, $blue];
	 * $cmyk_color = [$cyan, $magenta, $yellow, $key]; // In CMYK the black color is called Key.
	 * 
     * @param ?int $int_red
     * @param ?int $int_green
     * @param ?int $int_blue
     */
    public function SetFillColor()
    {
        $args = func_get_args();
		$str_fill_color = $this->getColourString($args);
		if($str_fill_color === $this->str_fill_color){
			return;
		}
		$this->str_fill_color = $str_fill_color;
        $this->bol_fill_text_differ = ($this->str_fill_color != $this->str_text_color);
        if ($this->int_page > 0) {
            $this->Out($this->str_fill_color);
        }
    }

    /**
	 * Support for use CMYK colors
	 * If you pass no parameter, you get black.
	 * If you pass 1 parameter, it is a grayscale component (between 0 and 100).
	 * If you pass 3 parameters, they are RGB components (between 0 and 255).
	 * If you pass 4 parameters, they are CMYK components (between 0 and 100).
	 * Can you pass the color values as an array:
	 * $rgb_color = [$red, $green, $blue];
	 * $cmyk_color = [$cyan, $magenta, $yellow, $key]; // In CMYK the black color is called Key.
	 * 
     * @param ?int $int_red
     * @param ?int $int_green
     * @param ?int $int_blue
     */
    public function SetTextColor()
    {
        $args = func_get_args();
		$str_text_color = $this->getColourString($args);
		if($str_text_color === $this->str_text_color){
			return;
		}
		$this->str_text_color = $str_text_color;
        $this->bol_fill_text_differ = ($this->str_fill_color != $this->str_text_color);
    }

    /**
     * @param string $str_text
     * @return float|int
     */
    public function GetStringWidth($str_text)
    {
        // Get width of a string in the current font
        $str_text = (string)$str_text;
        $arr_character_width = &$this->arr_current_font_info['cw'];
        $flt_width = 0;
        if ($this->bol_uniform_subset) {
            $str_unicode = $this->UTF8StringToArray($str_text);
            foreach ($str_unicode as $str_char) {
                if (isset($arr_character_width[$str_char])) {
                    $flt_width += (ord($arr_character_width[2 * $str_char]) << 8) + ord($arr_character_width[2 * $str_char + 1]);
                } else {
                    if ($str_char > 0 && $str_char < 128 && isset($arr_character_width[chr($str_char)])) {
                        $flt_width += $arr_character_width[chr($str_char)];
                    } else {
                        if (isset($this->arr_current_font_info['desc']['MissingWidth'])) {
                            $flt_width += $this->arr_current_font_info['desc']['MissingWidth'];
                        } else {
                            if (isset($this->arr_current_font_info['MissingWidth'])) {
                                $flt_width += $this->arr_current_font_info['MissingWidth'];
                            } else {
                                $flt_width += 500;
                            }
                        }
                    }
                }
            }
        } else {
            $int_length = strlen($str_text);
            for ($i = 0; $i < $int_length; $i++) {
                $flt_width += $arr_character_width[$str_text[$i]];
            }
        }
        return $flt_width * $this->int_font_size_user / 1000;
    }

    /**
     * @param float $flt_width
     */
    public function SetLineWidth($flt_width)
    {
        // Set line width
        $this->flt_line_width = $flt_width;
        if ($this->int_page > 0) {
            $this->Out(sprintf('%.2F w', $flt_width * $this->flt_scale_factor));
        }
    }

    /**
     * @param float $flt_x_1
     * @param float $flt_y_1
     * @param float $flt_x_2
     * @param float $flt_y_2
     */
    public function Line($flt_x_1, $flt_y_1, $flt_x_2, $flt_y_2)
    {
        // Draw a line
        $this->Out(sprintf('%.2F %.2F m %.2F %.2F l S', $flt_x_1 * $this->flt_scale_factor,
            ($this->flt_current_height - $flt_y_1) * $this->flt_scale_factor, $flt_x_2 * $this->flt_scale_factor,
            ($this->flt_current_height - $flt_y_2) * $this->flt_scale_factor));
    }

    /**
     * @param float $flt_x
     * @param float $flt_y
     * @param float $flt_width
     * @param float $flt_height
     * @param string $str_style
     */
    public function Rect($flt_x, $flt_y, $flt_width, $flt_height, $str_style = '')
    {
        // Draw a rectangle
        if ($str_style == 'F') {
            $op = 'f';
        } elseif ($str_style == 'FD' || $str_style == 'DF') {
            $op = 'B';
        } else {
            $op = 'S';
        }
        $this->Out(sprintf('%.2F %.2F %.2F %.2F re %s', $flt_x * $this->flt_scale_factor,
            ($this->flt_current_height - $flt_y) * $this->flt_scale_factor, $flt_width * $this->flt_scale_factor,
            -$flt_height * $this->flt_scale_factor, $op));
    }

    /**
     * Add a unicode font to the document
     *
     * @param string $fontFamily The Font-Family name to be used in setFont method
     * @param string $fontStyle Font-Style of the font to be used in setFont method (B, I, U)
     * @param string $fontFile The relative font filename used with the set font path
     */
    public function AddFont(string $fontFamily, string $fontStyle = '', string $fontFile = '') {

    // Add a TrueType, OpenType or Type1 font
        $fontFamily = strtolower($fontFamily);
        $fontStyle = strtoupper($fontStyle);

        if ($fontStyle == 'IB') {
            $fontStyle = 'BI';
        }
        if ($fontFile == '') {
            $fontFile = str_replace(' ', '', $fontFamily) . strtolower($fontStyle);

            if(file_exists($fontFile.'.ttf')) {
                $fontFile .= '.ttf';
            } else {
                $fontFile .= '.php';
            }
        }

        $fontExtension = pathinfo($fontFile, PATHINFO_EXTENSION);
        $isUnicode = (strtolower($fontExtension) != 'php');

        $fontKey = $fontFamily . $fontStyle;
        if (isset($this->arr_fonts[$fontKey])) {
            return;
        }

        if ($isUnicode) {
            $fontFile = $this->getFontPath() . $fontFile;

            $fontMetrics = $this->getFontMetricFiles($fontFile, $fontKey);

            $int_font_count = count($this->arr_fonts) + 1;
            $arr_numbers = range(0, (!empty($this->str_alias_number_pages)) ? 57 : 32);

            $this->arr_fonts[$fontKey] = array_merge($fontMetrics, [
                'i'           => $int_font_count,
                'ttffile'     => $fontFile,
                'subset'      => $arr_numbers,
                'filename'    => pathinfo($fontFile, PATHINFO_FILENAME),
            ]);

            $this->arr_font_files[$fontKey] = [
                'length1' => $fontMetrics['originalsize'],
                'type'    => self::FONT_TRUETYPE,
                'ttffile' => $fontFile,
            ];
            $this->arr_font_files[$fontFile] = [
                'type' => self::FONT_TRUETYPE
            ];

            unset($fontMetrics);
        } else {
            $arr_info = $this->LoadFont($fontFile);
            $arr_info['i'] = count($this->arr_fonts) + 1;
            if (!empty($arr_info['diff'])) {
                // Search existing encodings
                $int_key = array_search($arr_info['diff'], $this->arr_encoding_diffs);
                if (!$int_key) {
                    $int_key = count($this->arr_encoding_diffs) + 1;
                    $this->arr_encoding_diffs[$int_key] = $arr_info['diff'];
                }
                $arr_info['diffn'] = $int_key;
            }
            if (!empty($arr_info['file'])) {
                // Embedded font
                if ($arr_info['type'] == 'TrueType') {
                    $this->arr_font_files[$arr_info['file']] = array('length1' => $arr_info['originalsize']);
                } else {
                    $this->arr_font_files[$arr_info['file']] = array(
                        'length1' => $arr_info['size1'],
                        'length2' => $arr_info['size2']
                    );
                }
            }
            $this->arr_fonts[$fontKey] = $arr_info;
        }
    }

    /**
     * @param $fileName
     */
    public function getFontMetricFiles($fontFile, $fontKey) {

        $ext = strrchr($fontFile, '.');
        $baseName = basename($fontFile, $ext);

        $cachePath = $this->getCachePath();

        $metricFile = $cachePath . self::FILE_FONT_METRICS . $baseName . '.json';
        $charWidthFile = $cachePath . self::FILE_CHARACTER_WIDTH . $baseName . '.dat';

        $oFileSize = filesize($fontFile);

        if($cachePath !== null && file_exists($metricFile) && file_exists($charWidthFile)) {
            $fopen = fopen($metricFile, 'r');
            $fread = fread($fopen, filesize($metricFile) + 1);
            fclose($fopen);

            $json = json_decode($fread, true);

            if(json_last_error() === JSON_ERROR_NONE) {
                $fopen = fopen($charWidthFile, 'r');
                $fread = fread($fopen, filesize($charWidthFile) + 1);
                fclose($fopen);

                $json['cw'] = $fread;

                if($oFileSize == $json['originalsize']) {
                    return $json;
                }
            }
        }

        $ttf = new TTFontFile();
        $ttf->getMetrics($fontFile);

        $name = preg_replace('/[ ()]/', '', $ttf->getFullName());

        $flt_underline_pos = round($ttf->getUnderlinePosition());
        $flt_underline_thickness = round($ttf->getUnderlineThickness());

        $characterWidths = $ttf->getCharWidths();

    // Generate metrics array
        $strMetricsData = [
            'name'                    => $name,
            'type'                    => self::FONT_TRUETYPE,
            'desc'                    => [
                'Ascent'       => round($ttf->getAscent()),
                'Descent'      => round($ttf->getDescent()),
                'CapHeight'    => round($ttf->getCapHeight()),
                'Flags'        => $ttf->getFlags(),
                'FontBBox'     => '[' . round($ttf->getBbox()[0]) . ' ' . round($ttf->getBbox()[1]) . ' ' . round($ttf->getBbox()[2]) . ' ' . round($ttf->getBbox()[3]) . ']',
                'ItalicAngle'  => $ttf->getItalicAngle(),
                'StemV'        => round($ttf->getStemV()),
                'MissingWidth' => round($ttf->getDefaultWidth()),
            ],
            'flt_underline_pos'       => $flt_underline_pos,
            'flt_underline_thickness' => $flt_underline_thickness,
            'ttffile'                 => str_replace(__DIR__ . "/", "", $metricFile),
            'originalsize'            => $oFileSize,
            'fontkey'                 => $fontKey,
            'cw'                      => null,
        ];

        $cachePath = $this->getCachePath();
        if($cachePath !== null) {

        // write metrics file
            $json = json_encode($strMetricsData);

            $fopen = fopen($metricFile, 'w');
            fwrite($fopen, $json);
            fclose($fopen);

        // write char width file
            $fopen = fopen($charWidthFile, 'w');
            fwrite($fopen, $characterWidths);
            fclose($fopen);

        // unlink char width 127 file
            $charWidth127file = $cachePath.self::FILE_CHARACTER_WIDTH.$baseName.'.json';
            if(file_exists($charWidth127file)) {
                @unlink($charWidth127file);
            }
        }

        $strMetricsData['cw'] = $characterWidths;

        unset($ttf);

        return $strMetricsData;
    }

    /**
     * @param $str_family
     * @param string $str_style
     * @param int $int_size
     */
    public function SetFont($str_family, $str_style = '', $int_size = 0)
    {
        // Select a font; size given in points
        if ($str_family == '') {
            $str_family = $this->str_current_font_family;
        } else {
            $str_family = strtolower($str_family);
        }
        $str_style = strtoupper($str_style);
        if (strpos($str_style, 'U') !== false) {
            $this->bol_underline = true;
            $str_style = str_replace('U', '', $str_style);
        } else {
            $this->bol_underline = false;
        }
        if ($str_style == 'IB') {
            $str_style = 'BI';
        }
        if ($int_size == 0) {
            $int_size = $this->int_current_font_size;
        }
        // Test if font is already selected
        if ($this->str_current_font_family == $str_family && $this->str_current_font_style == $str_style && $this->int_current_font_size == $int_size) {
            return;
        }
        // Test if font is already loaded
        $str_font_key = $str_family . $str_style;
        if (!isset($this->arr_fonts[$str_font_key])) {
            // Test if one of the core fonts
            if ($str_family == 'arial') {
                $str_family = 'helvetica';
            }
            if (!in_array($str_family, $this->arr_core_fonts)) {
                throw new FPDFException('Undefined font: '.$str_family.' '.$str_style, FPDFException::UNDEFINED_FONT);
            }

            if ($str_family == 'symbol' || $str_family == 'zapfdingbats') {
                $str_style = '';
            }
            $str_font_key = $str_family . $str_style;
            if (!isset($this->arr_fonts[$str_font_key])) {
                $this->AddFont($str_family, $str_style);
            }
        }
        // Select it
        $this->str_current_font_family = $str_family;
        $this->str_current_font_style = $str_style;
        $this->int_current_font_size = $int_size;
        $this->int_font_size_user = $int_size / $this->flt_scale_factor;
        $this->arr_current_font_info = &$this->arr_fonts[$str_font_key];
        if ($this->arr_fonts[$str_font_key]['type'] == self::FONT_TRUETYPE) {
            $this->bol_uniform_subset = true;
        } else {
            $this->bol_uniform_subset = false;
        }
        if ($this->int_page > 0) {
            $this->Out(sprintf('BT /F%d %.2F Tf ET', $this->arr_current_font_info['i'], $this->int_current_font_size));
        }
    }

    /**
     * @param $int_size
     */
    public function SetFontSize($int_size)
    {
        // Set font size in points
        if ($this->int_current_font_size == $int_size) {
            return;
        }
        $this->int_current_font_size = $int_size;
        $this->int_font_size_user = $int_size / $this->flt_scale_factor;
        if ($this->int_page > 0) {
            $this->Out(sprintf('BT /F%d %.2F Tf ET', $this->arr_current_font_info['i'], $this->int_current_font_size));
        }
    }

    /**
	 * 
	 * @return int
	 */
    public function GetCurrentFontSize() {
        return $this->int_current_font_size;
    }

    /**
	 * 
	 * @return int
	 */
    public function GetUserFontSize() {
        return $this->int_current_font_size / $this->flt_scale_factor;
    }

    /**
	 * 
	 * @return int
	 */
    public function GetScaleFactor() {
        return $this->flt_scale_factor;
    }

    /**
     * @return int
     */
    public function AddLink()
    {
        // Create a new internal link
        $int_count = count($this->arr_internal_links) + 1;
        $this->arr_internal_links[$int_count] = array(0, 0);
        return $int_count;
    }

    /**
     * @param $mix_link_key
     * @param int $int_y
     * @param int $int_page
     */
    public function SetLink($mix_link_key, $int_y = 0, $int_page = -1)
    {
        // Set destination of internal link
        if ($int_y == -1) {
            $int_y = $this->flt_position_y;
        }
        if ($int_page == -1) {
            $int_page = $this->int_page;
        }
        $this->arr_internal_links[$mix_link_key] = array($int_page, $int_y);
    }

    /**
     * @param float $flt_x
     * @param float $flt_y
     * @param float $flt_width
     * @param float $flt_height
     * @param $mix_link_key
     */
    public function Link($flt_x, $flt_y, $flt_width, $flt_height, $mix_link_key)
    {
        // Put a link on the page
        $this->arr_page_links[$this->int_page][] = array(
            $flt_x * $this->flt_scale_factor,
            $this->flt_height_points - $flt_y * $this->flt_scale_factor,
            $flt_width * $this->flt_scale_factor,
            $flt_height * $this->flt_scale_factor,
            $mix_link_key
        );
    }

    /**
     * @param float $flt_x
     * @param float $flt_y
     * @param string $str_text
     */
    public function Text($flt_x, $flt_y, $str_text)
    {
        // Output a string
        if ($this->bol_uniform_subset) {
            $str_text_2 = '(' . $this->EscapeString($this->UTF8ToUTF16BE($str_text, false)) . ')';
            foreach ($this->UTF8StringToArray($str_text) as $uni) {
                $this->arr_current_font_info['subset'][$uni] = $uni;
            }
        } else {
            $str_text_2 = '(' . $this->EscapeString($str_text) . ')';
        }
        $str_output = sprintf('BT %.2F %.2F Td %s Tj ET', $flt_x * $this->flt_scale_factor,
            ($this->flt_current_height - $flt_y) * $this->flt_scale_factor, $str_text_2);
        if ($this->bol_underline && $str_text != '') {
            $str_output .= ' ' . $this->DoUnderline($flt_x, $flt_y, $str_text);
        }
        if ($this->bol_fill_text_differ) {
            $str_output = 'q ' . $this->str_text_color . ' ' . $str_output . ' Q';
        }
        $this->Out($str_output);
    }

    /**
     * @return bool
     */
    public function AcceptPageBreak()
    {
        // Accept automatic page break or not
        return $this->bol_auto_page_break;
    }

    /**
     * @param float $flt_width
     * @param float $flt_height
     * @param string $str_text
     * @param int $int_border
     * @param int $int_line_number
     * @param string $str_alignment
     * @param bool $bol_fill
     * @param string $str_link
     */
    public function Cell($flt_width, $flt_height = 0, $str_text = '', $int_border = 0, $int_line_number = 0, $str_alignment = '', $bol_fill = false, $str_link = '')
    {
        // Output a cell
        $flt_scale = $this->flt_scale_factor;
        if ($this->flt_position_y + $flt_height > $this->flt_page_break_trigger && !$this->bol_in_header && !$this->bol_in_footer && $this->AcceptPageBreak()) {
            // Automatic page break
            $flt_position_x = $this->flt_position_x;
            $int_word_spacing = $this->int_word_spacing;
            if ($int_word_spacing > 0) {
                $this->int_word_spacing = 0;
                $this->Out('0 Tw');
            }
            $this->AddPage($this->str_current_orientation, $this->arr_current_page_sizes);
            $this->flt_position_x = $flt_position_x;
            if ($int_word_spacing > 0) {
                $this->int_word_spacing = $int_word_spacing;
                $this->Out(sprintf('%.3F Tw', $int_word_spacing * $flt_scale));
            }
        }
        if ($flt_width == 0) {
            $flt_width = $this->flt_current_width - $this->int_right_margin - $this->flt_position_x;
        }
        $str_output = '';
        if ($bol_fill || $int_border == 1) {
            if ($bol_fill) {
                $str_operation = ($int_border == 1) ? 'B' : 'f';
            } else {
                $str_operation = 'S';
            }
            $str_output = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->flt_position_x * $flt_scale,
                ($this->flt_current_height - $this->flt_position_y) * $flt_scale, $flt_width * $flt_scale, -$flt_height * $flt_scale, $str_operation);
        }
        if (is_string($int_border)) {
            $flt_position_x = $this->flt_position_x;
            $flt_position_y = $this->flt_position_y;
            if (strpos($int_border, 'L') !== false) {
                $str_output .= sprintf('%.2F %.2F m %.2F %.2F l S ', $flt_position_x * $flt_scale, ($this->flt_current_height - $flt_position_y) * $flt_scale, $flt_position_x * $flt_scale,
                    ($this->flt_current_height - ($flt_position_y + $flt_height)) * $flt_scale);
            }
            if (strpos($int_border, 'T') !== false) {
                $str_output .= sprintf('%.2F %.2F m %.2F %.2F l S ', $flt_position_x * $flt_scale, ($this->flt_current_height - $flt_position_y) * $flt_scale,
                    ($flt_position_x + $flt_width) * $flt_scale, ($this->flt_current_height - $flt_position_y) * $flt_scale);
            }
            if (strpos($int_border, 'R') !== false) {
                $str_output .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($flt_position_x + $flt_width) * $flt_scale, ($this->flt_current_height - $flt_position_y) * $flt_scale,
                    ($flt_position_x + $flt_width) * $flt_scale, ($this->flt_current_height - ($flt_position_y + $flt_height)) * $flt_scale);
            }
            if (strpos($int_border, 'B') !== false) {
                $str_output .= sprintf('%.2F %.2F m %.2F %.2F l S ', $flt_position_x * $flt_scale, ($this->flt_current_height - ($flt_position_y + $flt_height)) * $flt_scale,
                    ($flt_position_x + $flt_width) * $flt_scale, ($this->flt_current_height - ($flt_position_y + $flt_height)) * $flt_scale);
            }
        }
        if ($str_text !== '') {
            if ($str_alignment == 'R') {
                $flt_dimensions = $flt_width - $this->int_cell_margin - $this->GetStringWidth($str_text);
            } elseif ($str_alignment == 'C') {
                $flt_dimensions = ($flt_width - $this->GetStringWidth($str_text)) / 2;
            } else {
                $flt_dimensions = $this->int_cell_margin;
            }
            if ($this->bol_fill_text_differ) {
                $str_output .= 'q ' . $this->str_text_color . ' ';
            }

            // If multibyte, Tw has no effect - do word spacing using an adjustment before each space
            if ($this->int_word_spacing && $this->bol_uniform_subset) {
                foreach ($this->UTF8StringToArray($str_text) as $mix_unicode) {
                    $this->arr_current_font_info['subset'][$mix_unicode] = $mix_unicode;
                }
                $str_space = $this->EscapeString($this->UTF8ToUTF16BE(' ', false));
                $str_output .= sprintf('BT 0 Tw %.2F %.2F Td [', ($this->flt_position_x + $flt_dimensions) * $flt_scale,
                    ($this->flt_current_height - ($this->flt_position_y + .5 * $flt_height + .3 * $this->int_font_size_user)) * $flt_scale);
                $arr_bits = explode(' ', $str_text);
                $int_bit_count = count($arr_bits);
                for ($i = 0; $i < $int_bit_count; $i++) {
                    $str_converted_text = $arr_bits[$i];
                    $str_converted_text = '(' . $this->EscapeString($this->UTF8ToUTF16BE($str_converted_text, false)) . ')';
                    $str_output .= sprintf('%s ', $str_converted_text);
                    if (($i + 1) < $int_bit_count) {
                        $flt_adjustment = -($this->int_word_spacing * $this->flt_scale_factor) * 1000 / $this->int_current_font_size;
                        $str_output .= sprintf('%d(%s) ', $flt_adjustment, $str_space);
                    }
                }
                $str_output .= '] TJ';
                $str_output .= ' ET';
            } else {
                if ($this->bol_uniform_subset) {
                    $str_text_2 = '(' . $this->EscapeString($this->UTF8ToUTF16BE($str_text, false)) . ')';
                    foreach ($this->UTF8StringToArray($str_text) as $mix_unicode) {
                        $this->arr_current_font_info['subset'][$mix_unicode] = $mix_unicode;
                    }
                } else {
                    $str_text_2 = '(' . str_replace(')', '\\)',
                            str_replace('(', '\\(', str_replace('\\', '\\\\', $str_text))) . ')';
                }
                $str_output .= sprintf('BT %.2F %.2F Td %s Tj ET', ($this->flt_position_x + $flt_dimensions) * $flt_scale,
                    ($this->flt_current_height - ($this->flt_position_y + .5 * $flt_height + .3 * $this->int_font_size_user)) * $flt_scale,
                    $str_text_2);
            }
            if ($this->bol_underline) {
                $str_output .= ' ' . $this->DoUnderline($this->flt_position_x + $flt_dimensions,
                        $this->flt_position_y + .5 * $flt_height + .3 * $this->int_font_size_user, $str_text);
            }
            if ($this->bol_fill_text_differ) {
                $str_output .= ' Q';
            }
            if ($str_link) {
                $this->Link($this->flt_position_x + $flt_dimensions,
                    $this->flt_position_y + .5 * $flt_height - .5 * $this->int_font_size_user, $this->GetStringWidth($str_text),
                    $this->int_font_size_user, $str_link);
            }
        }
        if ($str_output) {
            $this->Out($str_output);
        }
        $this->flt_last_cell_height = $flt_height;
        if ($int_line_number > 0) {
            // Go to next line
            $this->flt_position_y += $flt_height;
            if ($int_line_number == 1) {
                $this->flt_position_x = $this->int_left_margin;
            }
        } else {
            $this->flt_position_x += $flt_width;
        }
    }

    /**
     * @param float $flt_width
     * @param float $flt_height
     * @param string $str_text
     * @param int $int_border
     * @param string $str_alignment
     * @param bool $bol_fill
     * @param int int_maxline
     *
     * @return string
     */
    public function MultiCell($flt_width, $flt_height, $str_text, $int_border = 0, $str_alignment = 'J', $bol_fill = false, $int_maxline = 0)
    {
        // Output text with automatic or explicit line breaks
        $arr_character_width = &$this->arr_current_font_info['cw'];
        if ($flt_width == 0) {
            $flt_width = $this->flt_current_width - $this->int_right_margin - $this->flt_position_x;
        }
        $flt_max_width = ($flt_width - 2 * $this->int_cell_margin);
        $str_text = str_replace("\r", '', $str_text);
        if ($this->bol_uniform_subset) {
            $int_length = mb_strlen($str_text, 'utf-8');
            while ($int_length > 0 && mb_substr($str_text, $int_length - 1, 1, 'utf-8') == "\n") {
                $int_length--;
            }
        } else {
            $int_length = strlen($str_text);
            if ($int_length > 0 && $str_text[$int_length - 1] == "\n") {
                $int_length--;
            }
        }
        $mix_adjusted_border = 0;
        $mix_adjusted_border_2 = 0;
        if ($int_border) {
            if ($int_border == 1) {
                $int_border = 'LTRB';
                $mix_adjusted_border = 'LRT';
                $mix_adjusted_border_2 = 'LR';
            } else {
                $mix_adjusted_border_2 = '';
                if (strpos($int_border, 'L') !== false) {
                    $mix_adjusted_border_2 .= 'L';
                }
                if (strpos($int_border, 'R') !== false) {
                    $mix_adjusted_border_2 .= 'R';
                }
                $mix_adjusted_border = (strpos($int_border, 'T') !== false) ? $mix_adjusted_border_2 . 'T' : $mix_adjusted_border_2;
            }
        }
        $int_sep = -1;
        $int_i = 0;
        $int_j = 0;
        $int_l = 0;
        $int_space_count = 0;
        $int_line_count = 1;
        $int_ls = 0;
        while ($int_i < $int_length) {
            // Get next character
            if ($this->bol_uniform_subset) {
                $str_character = mb_substr($str_text, $int_i, 1, 'UTF-8');
            } else {
                $str_character = $str_text[$int_i];
            }
            if ($str_character == "\n") {
                // Explicit line break
                if ($this->int_word_spacing > 0) {
                    $this->int_word_spacing = 0;
                    $this->Out('0 Tw');
                }
                if ($this->bol_uniform_subset) {
                    $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_i - $int_j, 'UTF-8'), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
                } else {
                    $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_i - $int_j), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
                }
                $int_i++;
                $int_sep = -1;
                $int_j = $int_i;
                $int_l = 0;
                $int_space_count = 0;
                $int_line_count++;
                if ($int_border && $int_line_count == 2) {
                    $mix_adjusted_border = $mix_adjusted_border_2;
                }
                if($int_maxline && $int_line_count > $int_maxline) {
                    return substr($str_text, $int_i);
                }
                continue;
            }
            if ($str_character == ' ') {
                $int_sep = $int_i;
                $int_ls = $int_l;
                $int_space_count++;
            }

            if ($this->bol_uniform_subset) {
                $int_l += $this->GetStringWidth($str_character);
            } else {
                $int_l += $arr_character_width[$str_character] * $this->int_font_size_user / 1000;
            }

            if ($int_l > $flt_max_width) {
                // Automatic line break
                if ($int_sep == -1) {
                    if ($int_i == $int_j) {
                        $int_i++;
                    }
                    if ($this->int_word_spacing > 0) {
                        $this->int_word_spacing = 0;
                        $this->Out('0 Tw');
                    }
                    if ($this->bol_uniform_subset) {
                        $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_i - $int_j, 'UTF-8'), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
                    } else {
                        $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_i - $int_j), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
                    }
                } else {
                    if ($str_alignment == 'J') {
                        $this->int_word_spacing = ($int_space_count > 1) ? ($flt_max_width - $int_ls) / ($int_space_count - 1) : 0;
                        $this->Out(sprintf('%.3F Tw', $this->int_word_spacing * $this->flt_scale_factor));
                    }
                    if ($this->bol_uniform_subset) {
                        $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_sep - $int_j, 'UTF-8'), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
                    } else {
                        $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_sep - $int_j), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
                    }
                    $int_i = $int_sep + 1;
                }
                $int_sep = -1;
                $int_j = $int_i;
                $int_l = 0;
                $int_space_count = 0;
                $int_line_count++;
                if ($int_border && $int_line_count == 2) {
                    $mix_adjusted_border = $mix_adjusted_border_2;
                }

                if ($int_maxline && $int_line_count > $int_maxline) {
                    if ($this->int_word_spacing > 0) {
                        $this->int_word_spacing = 0;
                        $this->Out('0 Tw');
                    }
                    return substr($str_text, $int_i);
                }

            } else {
                $int_i++;
            }
        }
        // Last chunk
        if ($this->int_word_spacing > 0) {
            $this->int_word_spacing = 0;
            $this->Out('0 Tw');
        }
        if ($int_border && strpos($int_border, 'B') !== false) {
            $mix_adjusted_border .= 'B';
        }
        if ($this->bol_uniform_subset) {
            $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_i - $int_j, 'UTF-8'), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
        } else {
            $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_i - $int_j), $mix_adjusted_border, 2, $str_alignment, $bol_fill);
        }
        $this->flt_position_x = $this->int_left_margin;
        return '';
    }

    /**
     * @param float $flt_height
     * @param string $str_text
     * @param string $str_link
     */
    public function Write($flt_height, $str_text, $str_link = '')
    {
        // Output text in flowing mode
        $arr_character_widths = &$this->arr_current_font_info['cw'];
        $flt_width = $this->flt_current_width - $this->int_right_margin - $this->flt_position_x;

        $flt_max_width = ($flt_width - 2 * $this->int_cell_margin);
        $str_text = str_replace("\r", '', $str_text);
        if ($this->bol_uniform_subset) {
            $int_length = mb_strlen($str_text, 'UTF-8');
            if ($int_length == 1 && $str_text == " ") {
                $this->flt_position_x += $this->GetStringWidth($str_text);
                return;
            }
        } else {
            $int_length = strlen($str_text);
        }
        $int_sep = -1;
        $int_i = 0;
        $int_j = 0;
        $flt_string_width = 0;
        $int_line_count = 1;
        while ($int_i < $int_length) {
            // Get next character
            if ($this->bol_uniform_subset) {
                $str_character = mb_substr($str_text, $int_i, 1, 'UTF-8');
            } else {
                $str_character = $str_text[$int_i];
            }
            if ($str_character == "\n") {
                // Explicit line break
                if ($this->bol_uniform_subset) {
                    $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_i - $int_j, 'UTF-8'), 0, 2, '', 0, $str_link);
                } else {
                    $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_i - $int_j), 0, 2, '', 0, $str_link);
                }
                $int_i++;
                $int_sep = -1;
                $int_j = $int_i;
                $flt_string_width = 0;
                if ($int_line_count == 1) {
                    $this->flt_position_x = $this->int_left_margin;
                    $flt_width = $this->flt_current_width - $this->int_right_margin - $this->flt_position_x;
                    $flt_max_width = ($flt_width - 2 * $this->int_cell_margin);
                }
                $int_line_count++;
                continue;
            }
            if ($str_character == ' ') {
                $int_sep = $int_i;
            }

            if ($this->bol_uniform_subset) {
                $flt_string_width += $this->GetStringWidth($str_character);
            } else {
                $flt_string_width += $arr_character_widths[$str_character] * $this->int_font_size_user / 1000;
            }

            if ($flt_string_width > $flt_max_width) {
                // Automatic line break
                if ($int_sep == -1) {
                    if ($this->flt_position_x > $this->int_left_margin) {
                        // Move to next line
                        $this->flt_position_x = $this->int_left_margin;
                        $this->flt_position_y += $flt_height;
                        $flt_width = $this->flt_current_width - $this->int_right_margin - $this->flt_position_x;
                        $flt_max_width = ($flt_width - 2 * $this->int_cell_margin);
                        $int_i++;
                        $int_line_count++;
                        continue;
                    }
                    if ($int_i == $int_j) {
                        $int_i++;
                    }
                    if ($this->bol_uniform_subset) {
                        $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_i - $int_j, 'UTF-8'), 0, 2, '', 0, $str_link);
                    } else {
                        $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_i - $int_j), 0, 2, '', 0, $str_link);
                    }
                } else {
                    if ($this->bol_uniform_subset) {
                        $this->Cell($flt_width, $flt_height, mb_substr($str_text, $int_j, $int_sep - $int_j, 'UTF-8'), 0, 2, '', 0, $str_link);
                    } else {
                        $this->Cell($flt_width, $flt_height, substr($str_text, $int_j, $int_sep - $int_j), 0, 2, '', 0, $str_link);
                    }
                    $int_i = $int_sep + 1;
                }
                $int_sep = -1;
                $int_j = $int_i;
                $flt_string_width = 0;
                if ($int_line_count == 1) {
                    $this->flt_position_x = $this->int_left_margin;
                    $flt_width = $this->flt_current_width - $this->int_right_margin - $this->flt_position_x;
                    $flt_max_width = ($flt_width - 2 * $this->int_cell_margin);
                }
                $int_line_count++;
            } else {
                $int_i++;
            }
        }
        // Last chunk
        if ($int_i != $int_j) {
            if ($this->bol_uniform_subset) {
                $this->Cell($flt_string_width, $flt_height, mb_substr($str_text, $int_j, $int_i - $int_j, 'UTF-8'), 0, 0, '', 0, $str_link);
            } else {
                $this->Cell($flt_string_width, $flt_height, substr($str_text, $int_j), 0, 0, '', 0, $str_link);
            }
        }
    }

    /**
     * @param float|null $flt_height
     */
    public function Ln($flt_height = null)
    {
        // Line feed; default value is last cell height
        $this->flt_position_x = $this->int_left_margin;
        if ($flt_height === null) {
            $this->flt_position_y += $this->flt_last_cell_height;
        } else {
            $this->flt_position_y += $flt_height;
        }
    }

    /**
     * @param $str_file
     * @param float|null $flt_x
     * @param float|null $flt_y
     * @param int $int_width
     * @param int $int_height
     * @param string $str_type
     * @param string $str_link
     */
    public function Image($str_file, $flt_x = null, $flt_y = null, $int_width = 0, $int_height = 0, $str_type = '', $str_link = '')
    {
        // Put an image on the page
        if (!isset($this->arr_images[$str_file])) {
            // First use of this image, get info
            if ($str_type == '') {
                $int_position = strrpos($str_file, '.');
                if (!$int_position) {
                    throw new FPDFException('Image file has no extension and no type was specified: ' . $str_file, FPDFException::INVALID_IMAGE);
                }
                $str_type = substr($str_file, $int_position + 1);
            }
            $str_type = strtolower($str_type);
            if ($str_type == 'jpeg') {
                $str_type = 'jpg';
            }
            $str_method = '_parse' . $str_type;
            if (!method_exists($this, $str_method)) {
                throw new FPDFException('Unsupported image type: `'.$str_type.'`', FPDFException::UNSUPPORTED_IMAGE);
            }
            $arr_image_info = $this->$str_method($str_file);
            $arr_image_info['i'] = count($this->arr_images) + 1;
            $this->arr_images[$str_file] = $arr_image_info;
        } else {
            $arr_image_info = $this->arr_images[$str_file];
        }

        // Automatic width and height calculation if needed
        if ($int_width == 0 && $int_height == 0) {
            // Put image at 96 dpi
            $int_width = -96;
            $int_height = -96;
        }
        if ($int_width < 0) {
            $int_width = -$arr_image_info['w'] * 72 / $int_width / $this->flt_scale_factor;
        }
        if ($int_height < 0) {
            $int_height = -$arr_image_info['h'] * 72 / $int_height / $this->flt_scale_factor;
        }
        if ($int_width == 0) {
            $int_width = $int_height * $arr_image_info['w'] / $arr_image_info['h'];
        }
        if ($int_height == 0) {
            $int_height = $int_width * $arr_image_info['h'] / $arr_image_info['w'];
        }

        // Flowing mode
        if ($flt_y === null) {
            if ($this->flt_position_y + $int_height > $this->flt_page_break_trigger && !$this->bol_in_header && !$this->bol_in_footer && $this->AcceptPageBreak()) {
                // Automatic page break
                $flt_new_x_pos = $this->flt_position_x;
                $this->AddPage($this->str_current_orientation, $this->arr_current_page_sizes);
                $this->flt_position_x = $flt_new_x_pos;
            }
            $flt_y = $this->flt_position_y;
            $this->flt_position_y += $int_height;
        }

        if ($flt_x === null) {
            $flt_x = $this->flt_position_x;
        }
        $this->Out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $int_width * $this->flt_scale_factor,
            $int_height * $this->flt_scale_factor, $flt_x * $this->flt_scale_factor,
            ($this->flt_current_height - ($flt_y + $int_height)) * $this->flt_scale_factor, $arr_image_info['i']));
        if ($str_link) {
            $this->Link($flt_x, $flt_y, $int_width, $int_height, $str_link);
        }
    }

    /**
     * @return mixed
     */
    public function GetX()
    {
        // Get x position
        return $this->flt_position_x;
    }

    /**
     * @param float $flt_position_x
     */
    public function SetX($flt_position_x)
    {
        // Set x position
        if ($flt_position_x >= 0) {
            $this->flt_position_x = $flt_position_x;
        } else {
            $this->flt_position_x = $this->flt_current_width + $flt_position_x;
        }
    }

    /**
     * @return float
     */
    public function GetY()
    {
        // Get y position
        return $this->flt_position_y;
    }

    /**
     * @param float $flt_position_y
     */
    public function SetY($flt_position_y)
    {
        // Set y position and reset x
        $this->flt_position_x = $this->int_left_margin;
        if ($flt_position_y >= 0) {
            $this->flt_position_y = $flt_position_y;
        } else {
            $this->flt_position_y = $this->flt_current_height + $flt_position_y;
        }
    }

    /**
     * @param $x
     * @param $y
     */
    public function SetXY($x, $y)
    {
        // Set x and y positions
        $this->SetY($y);
        $this->SetX($x);
    }
	
    /**
     * Get current page width
     *
     * @return float
     */
    public function GetPageWidth()
    {
        return $this->flt_current_width;
    }
	
    /**
     * Get current page height
     *
     * @return float
     */
    public function GetPageHeight()
    {
        return $this->flt_current_height;
    }
	
	
	function Output($name="", $_dest="S")
	{
		// Output PDF to some destination
		if ($this->int_state < self::DOCUMENT_STATE_TERMINATED) {
			$this->Close();
		}
		
		$dest = strtoupper($_dest);
		if($name==""){
			$name = "doc.pdf";
		}
		
		switch($dest)
		{
			case 'I':
				// Send to standard output
				$this->checkoutput();
				if(PHP_SAPI!='cli')
				{
					// We send to a browser
					header('Content-Type: application/pdf');
					header('Content-Disposition: inline; filename="'.$name.'"');
					header('Cache-Control: private, max-age=0, must-revalidate');
					header('Pragma: public');
				}
				echo $this->str_buffer;
				break;
			case 'D':
				// Download file
				$this->checkoutput();
				header('Content-Type: application/x-download');
				header('Content-Disposition: attachment; filename="'.$name.'"');
				header('Cache-Control: private, max-age=0, must-revalidate');
				header('Pragma: public');
				echo $this->str_buffer;
				break;
			case 'F':
				// Save to local file
				$f = fopen($name,'wb');
				if(!$f){
					throw new FPDFException('Unable to create output file `'.$name.'`', FPDFException::OUTPUT_INVALID_PATH);
				}
				fwrite($f,$this->str_buffer,strlen($this->str_buffer));
				fclose($f);
				break;
			case 'S':
				// Return as a string
				return $this->str_buffer;
			default:
				throw new FPDFException('Unable to create output file `'.$name.'`', FPDFException::OUTPUT_INVALID_DESTINATION);
		}
		return '';
	}

    /**
     * Set the font directory where font should be loaded front
     *
     * @param $fontPath The font directory
     */

    public function setFontPath(string $fontPath): string {
		$str_font_path = realpath($fontPath).'/';
        if(!file_exists($str_font_path) || !is_dir($str_font_path)) {
            throw new FPDFException('Font path does not exist `'.$str_font_path.'`', FPDFException::INVALID_FONT_PATH);
        }

        $this->str_font_path = $str_font_path;

        return $this->str_font_path;
    }

    /**
     * @return string
     */
    protected function getFontPath()
    {
        return realpath($this->str_font_path).'/';
    }

    /**
     *
     */
    protected function checkOutput()
    {
        if (PHP_SAPI !== 'cli') {
            if (headers_sent($str_file, $int_line)) {
                throw new FPDFException("Some data has already been output, can't send PDF file, output started at ".$str_file.":".$int_line, FPDFException::HEADER_ALREADY_SENT);
            }
        }

        if (ob_get_length()) {

        // The output buffer is not empty
            if (!preg_match('/^(\xEF\xBB\xBF)?\s*$/', ob_get_contents())) {
                throw new FPDFException('Some data has already been output, can\'t send PDF file', FPDFException::HEADER_ALREADY_SENT);
            }

        // It contains only a UTF-8 BOM and/or whitespace, let's clean it
            ob_clean();
        }
    }

    /**
     * @param $mix_size
     * @return array|string
     */
    private function getPageSize($mix_size)
    {
        if (is_string($mix_size)) {
            $mix_size = strtolower($mix_size);
            if (!isset($this->arr_standard_page_sizes[$mix_size])) {
                throw new FPDFException('Invalid page size: ' . $mix_size, FPDFException::INVALID_PAGE_SIZE);
            }
            $a = $this->arr_standard_page_sizes[$mix_size];
            return array($a[0] / $this->flt_scale_factor, $a[1] / $this->flt_scale_factor);
        } else {
            if ($mix_size[0] > $mix_size[1]) {
                return array($mix_size[1], $mix_size[0]);
            } else {
                return $mix_size;
            }
        }
    }

    /**
     * @param string $str_orientation
     * @param $mix_size
     */
    private function BeginPage($str_orientation, $mix_size)
    {
        $this->int_page++;
        $this->arr_pages[$this->int_page] = '';
        $this->int_state = self::DOCUMENT_STATE_CREATING;
        $this->flt_position_x = $this->int_left_margin;
        $this->flt_position_y = $this->int_top_margin;
        $this->str_current_font_family = '';
        // Check page size and orientation
        if ($str_orientation == '') {
            $str_orientation = $this->str_default_orientation;
        } else {
            $str_orientation = strtoupper($str_orientation[0]);
        }
        if ($mix_size == '') {
            $mix_size = $this->arr_default_page_sizes;
        } else {
            $mix_size = $this->getPageSize($mix_size);
        }
        if ($str_orientation != $this->str_current_orientation || $mix_size[0] != $this->arr_current_page_sizes[0] || $mix_size[1] != $this->arr_current_page_sizes[1]) {
            // New size or orientation
            if ($str_orientation == self::ORIENTATION_PORTRAIT) {
                $this->flt_current_width = $mix_size[0];
                $this->flt_current_height = $mix_size[1];
            } else { // landscape
                $this->flt_current_width = $mix_size[1];
                $this->flt_current_height = $mix_size[0];
            }
            $this->flt_width_points = $this->flt_current_width * $this->flt_scale_factor;
            $this->flt_height_points = $this->flt_current_height * $this->flt_scale_factor;
            $this->flt_page_break_trigger = $this->flt_current_height - $this->int_break_margin;
            $this->str_current_orientation = $str_orientation;
            $this->arr_current_page_sizes = $mix_size;
        }
        if ($str_orientation != $this->str_default_orientation || $mix_size[0] != $this->arr_default_page_sizes[0] || $mix_size[1] != $this->arr_default_page_sizes[1]) {
            $this->arr_page_sizes[$this->int_page] = array($this->flt_width_points, $this->flt_height_points);
        }
    }

    /**
     *
     */
    private function EndPage()
    {
        $this->int_state = self::DOCUMENT_STATE_INITIALIZED;
    }

    /**
     * @param string $str_font
     * @return array
     */
    private function LoadFont($str_font)
    {
        // Load a font definition file from the font directory
        include($this->str_font_path . $str_font);
        $arr_defined_vars = get_defined_vars();
        if (!isset($arr_defined_vars['name'])) {
            throw new FPDFException('Could not include font definition file', FPDFException::INVALID_FONT_FILE);
        }
        return $arr_defined_vars;
    }

    /**
     * @param string $str_text
     * @return mixed
     */
    protected function EscapeString($str_text)
    {
        // Escape special characters in strings
        $str_text = str_replace('\\', '\\\\', $str_text);
        $str_text = str_replace('(', '\\(', $str_text);
        $str_text = str_replace(')', '\\)', $str_text);
        $str_text = str_replace("\r", '\\r', $str_text);
        return $str_text;
    }

    /**
     * @param string $s
     * @return string
     */
    protected function TextString($s)
    {
		if($this->Protection->isEncrypted()){
			$s = $this->Protection::RC4($this->Protection->ObjectKey($this->int_current_object), $s);
		}
        // Format a text string
        return '(' . $this->EscapeString($s) . ')';
    }

    /**
     * @param string $str_text
     * @param bool $bol_set_byte_order_mark
     * @return string
     */
    protected function UTF8toUTF16($str_text, $bol_set_byte_order_mark = true)
    {
        // Convert UTF-8 to UTF-16BE with BOM
        $str_res = "\xFE\xFF";
		if(!$bol_set_byte_order_mark){
			$str_res = "";
		}
        $int_length = strlen($str_text);
        $i = 0;
        while ($i < $int_length) {
            $int_character_1 = ord($str_text[$i++]);
            if ($int_character_1 >= 224) {
                // 3-byte character
                $int_character_2 = ord($str_text[$i++]);
                $int_character_3 = ord($str_text[$i++]);
                $str_res .= chr((($int_character_1 & 0x0F) << 4) + (($int_character_2 & 0x3C) >> 2));
                $str_res .= chr((($int_character_2 & 0x03) << 6) + ($int_character_3 & 0x3F));
            } elseif ($int_character_1 >= 192) {
                // 2-byte character
                $int_character_2 = ord($str_text[$i++]);
                $str_res .= chr(($int_character_1 & 0x1C) >> 2);
                $str_res .= chr((($int_character_1 & 0x03) << 6) + ($int_character_2 & 0x3F));
            } else {
                // Single-byte character
                $str_res .= "\0" . chr($int_character_1);
            }
        }
        return $str_res;
    }

    protected function DoUnderline($flt_x, $flt_y, $str_text)
    {
        // Underline text
//		flt_underline_pos"]=> int(-113) ["flt_underline_thickness
        $flt_underline_position = ($this->arr_current_font_info['up']??($this->arr_current_font_info['flt_underline_pos']??0));
        $flt_underline_thickness = ($this->arr_current_font_info['ut']??($this->arr_current_font_info['flt_underline_thickness']??0));
        $flt_width = $this->GetStringWidth($str_text) + $this->int_word_spacing * substr_count($str_text, ' ');
        return sprintf('%.2F %.2F %.2F %.2F re f', $flt_x * $this->flt_scale_factor,
            ($this->flt_current_height - ($flt_y - $flt_underline_position / 1000 * $this->int_font_size_user)) * $this->flt_scale_factor,
            $flt_width * $this->flt_scale_factor, -$flt_underline_thickness / 1000 * $this->int_current_font_size);
    }

    /**
     * @param string $str_file
     * @return array
     */
    private function _parsejpg($str_file)
    {
        // Extract info from a JPEG file
        $arr_size_data = getimagesize($str_file);
        if (!$arr_size_data) {
            throw new FPDFException('Missing or incorrect image file: ' . $str_file, FPDFException::INVALID_IMAGE);
        }
        if ($arr_size_data[2] != 2) {
            throw new FPDFException('Not a JPEG file: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        if (!isset($arr_size_data['channels']) || $arr_size_data['channels'] == 3) {
            $str_color_space = 'DeviceRGB';
        } elseif ($arr_size_data['channels'] == 4) {
            $str_color_space = 'DeviceCMYK';
        } else {
            $str_color_space = 'DeviceGray';
        }
        $int_bits_per_component = isset($arr_size_data['bits']) ? $arr_size_data['bits'] : 8;
        $str_data = file_get_contents($str_file);
        return array('w' => $arr_size_data[0], 'h' => $arr_size_data[1], 'cs' => $str_color_space, 'bpc' => $int_bits_per_component, 'f' => 'DCTDecode', 'data' => $str_data);
    }

    /**
     * @param string $str_file
     * @return array
     */
    private function _parsepng($str_file)
    {
        // Extract info from a PNG file
        $ptr_file = fopen($str_file, 'rb');
        if (!$ptr_file) {
            throw new FPDFException('Can\'t open image file: ' . $str_file, FPDFException::INVALID_IMAGE);
        }
        $arr_info = $this->_parsepngstream($ptr_file, $str_file);
        fclose($ptr_file);
        return $arr_info;
    }

    /**
     * @param string $ptr_file
     * @param $str_file
     * @return array
     */
    private function _parsepngstream($ptr_file, $str_file)
    {
        // Check signature
        if ($this->_readstream($ptr_file, 8) != chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10)) {
            throw new FPDFException('Not a PNG file: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }

        // Read header chunk
        $this->_readstream($ptr_file, 4);
        if ($this->_readstream($ptr_file, 4) != 'IHDR') {
            throw new FPDFException('Incorrect PNG file: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        $int_width = $this->_readint($ptr_file);
        $int_height = $this->_readint($ptr_file);
        $int_bits_per_component = ord($this->_readstream($ptr_file, 1));
        if ($int_bits_per_component > 8) {
            throw new FPDFException('16-bit depth not supported: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        $int_color_channels = ord($this->_readstream($ptr_file, 1));
        if ($int_color_channels == 0 || $int_color_channels == 4) {
            $str_color_space = 'DeviceGray';
        } elseif ($int_color_channels == 2 || $int_color_channels == 6) {
            $str_color_space = 'DeviceRGB';
        } elseif ($int_color_channels == 3) {
            $str_color_space = 'Indexed';
        } else {
            throw new FPDFException('Unknown color type: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        if (ord($this->_readstream($ptr_file, 1)) != 0) {
            throw new FPDFException('Unknown compression method: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        if (ord($this->_readstream($ptr_file, 1)) != 0) {
            throw new FPDFException('Unknown filter method: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        if (ord($this->_readstream($ptr_file, 1)) != 0) {
            throw new FPDFException('Interlacing not supported: ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        $this->_readstream($ptr_file, 4);
        $str_predictor = '/Predictor 15 /Colors ' . ($str_color_space == 'DeviceRGB' ? 3 : 1) . ' /BitsPerComponent ' . $int_bits_per_component . ' /Columns ' . $int_width;

        // Scan chunks looking for palette, transparency and image data
        $str_palette = '';
        $arr_transparency_data = '';
        $str_data = '';
        do {
            $int_line = $this->_readint($ptr_file);
            $str_type = $this->_readstream($ptr_file, 4);
            if ($str_type == 'PLTE') {
                // Read palette
                $str_palette = $this->_readstream($ptr_file, $int_line);
                $this->_readstream($ptr_file, 4);
            } elseif ($str_type == 'tRNS') {
                // Read transparency info
                $str_transparency = $this->_readstream($ptr_file, $int_line);
                if ($int_color_channels == 0) {
                    $arr_transparency_data = array(ord(substr($str_transparency, 1, 1)));
                } elseif ($int_color_channels == 2) {
                    $arr_transparency_data = array(ord(substr($str_transparency, 1, 1)), ord(substr($str_transparency, 3, 1)), ord(substr($str_transparency, 5, 1)));
                } else {
                    $int_pos = strpos($str_transparency, chr(0));
                    if ($int_pos !== false) {
                        $arr_transparency_data = array($int_pos);
                    }
                }
                $this->_readstream($ptr_file, 4);
            } elseif ($str_type == 'IDAT') {
                // Read image data block
                $str_data .= $this->_readstream($ptr_file, $int_line);
                $this->_readstream($ptr_file, 4);
            } elseif ($str_type == 'IEND') {
                break;
            } else {
                $this->_readstream($ptr_file, $int_line + 4);
            }
        } while ($int_line);

        if ($str_color_space == 'Indexed' && empty($str_palette)) {
            throw new FPDFException('Missing palette in ' . $str_file, FPDFException::UNSUPPORTED_IMAGE);
        }
        $arr_info = array(
            'w' => $int_width,
            'h' => $int_height,
            'cs' => $str_color_space,
            'bpc' => $int_bits_per_component,
            'f' => 'FlateDecode',
            'dp' => $str_predictor,
            'pal' => $str_palette,
            'trns' => $arr_transparency_data
        );
        if ($int_color_channels >= 4) {
            // Extract alpha channel
            if (!function_exists('gzuncompress')) {
                throw new FPDFException('Zlib not available, can\'t handle alpha channel: ' . $str_file, FPDFException::EXTENSION_NOT_AVAILABLE);
            }
            $str_data = gzuncompress($str_data);
            $str_color = '';
            $str_alpha = '';
            if ($int_color_channels == 4) {
                // Gray image
                $int_length = 2 * $int_width;
                for ($i = 0; $i < $int_height; $i++) {
                    $int_pos = (1 + $int_length) * $i;
                    $str_color .= $str_data[$int_pos];
                    $str_alpha .= $str_data[$int_pos];
                    $str_line = substr($str_data, $int_pos + 1, $int_length);
                    $str_color .= preg_replace('/(.)./s', '$1', $str_line);
                    $str_alpha .= preg_replace('/.(.)/s', '$1', $str_line);
                }
            } else {
                // RGB image
                $int_length = 4 * $int_width;
                for ($i = 0; $i < $int_height; $i++) {
                    $int_pos = (1 + $int_length) * $i;
                    $str_color .= $str_data[$int_pos];
                    $str_alpha .= $str_data[$int_pos];
                    $str_line = substr($str_data, $int_pos + 1, $int_length);
                    $str_color .= preg_replace('/(.{3})./s', '$1', $str_line);
                    $str_alpha .= preg_replace('/.{3}(.)/s', '$1', $str_line);
                }
            }
            unset($str_data);
            $str_data = gzcompress($str_color);
            $arr_info['smask'] = gzcompress($str_alpha);
            if ($this->str_pdf_version < '1.4') {
                $this->str_pdf_version = '1.4';
            }
        }
        $arr_info['data'] = $str_data;
        return $arr_info;
    }

    /**
     * @param $ptr_file
     * @param int $int_bytes
     * @return string
     */
    private function _readstream($ptr_file, $int_bytes)
    {
        // Read n bytes from stream
        $str_result = '';
        while ($int_bytes > 0 && !feof($ptr_file)) {
            $str_data = fread($ptr_file, $int_bytes);
            if ($str_data === false) {
                throw new FPDFException('Error while reading stream', FPDFException::INVALID_STREAM);
            }
            $int_bytes -= strlen($str_data);
            $str_result .= $str_data;
        }
        if ($int_bytes > 0) {
            throw new FPDFException('Unexpected end of stream', FPDFException::INVALID_STREAM);
        }
        return $str_result;
    }

    /**
     * @param $ptr_file
     * @return mixed
     */
    private function _readint($ptr_file)
    {
        // Read a 4-byte integer from stream
        $arr_data = unpack('Ni', $this->_readstream($ptr_file, 4));
        return $arr_data['i'];
    }

    /**
     * @param string $str_file
     * @return array
     */
    private function _parsegif($str_file)
    {
        // Extract info from a GIF file (via PNG conversion)
        if (!function_exists('imagepng')) {
            throw new FPDFException('GD extension is required for GIF support', FPDFException::EXTENSION_NOT_AVAILABLE);
        }
        if (!function_exists('imagecreatefromgif')) {
            throw new FPDFException('GD has no GIF read support', FPDFException::EXTENSION_NOT_AVAILABLE);
        }
        $obj_image = imagecreatefromgif($str_file);
        if (!$obj_image) {
            throw new FPDFException('Missing or incorrect image file: ' . $str_file, FPDFException::INVALID_IMAGE);
        }
        imageinterlace($obj_image, 0);
        $ptr_file = @fopen('php://temp', 'rb+');
        if ($ptr_file) {
            // Perform conversion in memory
            ob_start();
            imagepng($obj_image);
            $str_data = ob_get_clean();
            imagedestroy($obj_image);
            fwrite($ptr_file, $str_data);
            rewind($ptr_file);
            $arr_info = $this->_parsepngstream($ptr_file, $str_file);
            fclose($ptr_file);
        } else {
            // Use temporary file
            $str_tmp = tempnam('.', 'gif');
            if (!$str_tmp) {
                throw new FPDFException('Unable to create a temporary file', FPDFException::IMAGE_NOT_WRITABLE);
            }
            if (!imagepng($obj_image, $str_tmp)) {
                throw new FPDFException('Error while saving to temporary file', FPDFException::IMAGE_NOT_WRITABLE);
            }
            imagedestroy($obj_image);
            $arr_info = $this->_parsepng($str_tmp);
            unlink($str_tmp);
        }
        return $arr_info;
    }

    /**
     * @param string $str_file
     * @return array
     */
    private function _parsewebp($str_file)
    {
        // Extract info from a GIF file (via PNG conversion)
        if (!function_exists('imagepng')) {
            throw new FPDFException('GD extension is required for GIF support', FPDFException::EXTENSION_NOT_AVAILABLE);
        }
        if (!function_exists('imagecreatefromwebp')) {
            throw new FPDFException('GD has no GIF read support', FPDFException::EXTENSION_NOT_AVAILABLE);
        }
        $obj_image = imagecreatefromwebp($str_file);
        if (!$obj_image) {
            throw new FPDFException('Missing or incorrect image file: ' . $str_file, FPDFException::INVALID_IMAGE);
        }
        imageinterlace($obj_image, 0);
        $ptr_file = @fopen('php://temp', 'rb+');
        if ($ptr_file) {
            // Perform conversion in memory
            ob_start();
            imagepng($obj_image);
            $str_data = ob_get_clean();
            imagedestroy($obj_image);
            fwrite($ptr_file, $str_data);
            rewind($ptr_file);
            $arr_info = $this->_parsepngstream($ptr_file, $str_file);
            fclose($ptr_file);
        } else {
            // Use temporary file
            $str_tmp = tempnam('.', 'webp');
            if (!$str_tmp) {
                throw new FPDFException('Unable to create a temporary file', FPDFException::IMAGE_NOT_WRITABLE);
            }
            if (!imagepng($obj_image, $str_tmp)) {
                throw new FPDFException('Error while saving to temporary file', FPDFException::IMAGE_NOT_WRITABLE);
            }
            imagedestroy($obj_image);
            $arr_info = $this->_parsepng($str_tmp);
            unlink($str_tmp);
        }
        return $arr_info;
    }

    /**
     * Creates a new object
     */
    protected function NewObject()
    {
        $this->int_current_object++;
        $this->arr_offsets[$this->int_current_object] = strlen($this->str_buffer);
        $this->Out($this->int_current_object . ' 0 obj');
    }

    /**
     * @param string $str_data
     */
    protected function PutStream($str_data)
    {
		if($this->Protection->isEncrypted()){
			$str_data = $this->Protection::RC4($this->Protection->ObjectKey($this->int_current_object), $str_data);
		}
		
        $this->Out('stream');
        $this->Out($str_data);
        $this->Out('endstream');
    }

    /**
     * @param string $str_data
     */
    protected function Out($str_data)
    {
        // Add a line to the document
        if ($this->int_state == self::DOCUMENT_STATE_CREATING) {
            $this->arr_pages[$this->int_page] .= $str_data . "\n";
        } else {
            $this->str_buffer .= $str_data . "\n";
        }
    }

    /**
     * Puts the pages into the document
     */
    protected function PutPages()
    {
        $int_page = $this->int_page;
        if (!empty($this->str_alias_number_pages)) {
            // Replace number of pages in fonts using subsets
            $str_alias = $this->UTF8ToUTF16BE($this->str_alias_number_pages, false);
            $str_replace = $this->UTF8ToUTF16BE("$int_page", false);
            for ($n = 1; $n <= $int_page; $n++) {
                $this->arr_pages[$n] = str_replace($str_alias, $str_replace, $this->arr_pages[$n]);
            }
            // Now repeat for no pages in non-subset fonts
            for ($n = 1; $n <= $int_page; $n++) {
                $this->arr_pages[$n] = str_replace($this->str_alias_number_pages, $int_page, $this->arr_pages[$n]);
            }
        }
        if ($this->str_default_orientation == self::ORIENTATION_PORTRAIT) {
            $flt_page_width = $this->arr_default_page_sizes[0] * $this->flt_scale_factor;
            $flt_page_height = $this->arr_default_page_sizes[1] * $this->flt_scale_factor;
        } else {
            $flt_page_width = $this->arr_default_page_sizes[1] * $this->flt_scale_factor;
            $flt_page_height = $this->arr_default_page_sizes[0] * $this->flt_scale_factor;
        }
        $str_filter = ($this->bol_compress) ? '/Filter /FlateDecode ' : '';
        for ($n = 1; $n <= $int_page; $n++) {
            // Page
            $this->NewObject();
            $this->Out('<</Type /Page');
            $this->Out('/Parent 1 0 R');
            if (isset($this->arr_page_sizes[$n])) {
                $this->Out(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->arr_page_sizes[$n][0],
                    $this->arr_page_sizes[$n][1]));
            }
            $this->Out('/Resources 2 0 R');
            if (isset($this->arr_page_links[$n])) {
                // Links
                $str_annotations = '/Annots [';
                foreach ($this->arr_page_links[$n] as $arr_page_link) {
                    $str_rectangle = sprintf('%.2F %.2F %.2F %.2F', $arr_page_link[0], $arr_page_link[1], $arr_page_link[0] + $arr_page_link[2], $arr_page_link[1] - $arr_page_link[3]);
                    $str_annotations .= '<</Type /Annot /Subtype /Link /Rect [' . $str_rectangle . '] /Border [0 0 0] ';
                    if (is_string($arr_page_link[4])) {
                        $str_annotations .= '/A <</S /URI /URI ' . $this->TextString($arr_page_link[4]) . '>>>>';
                    } else {
                        $arr_link = $this->arr_internal_links[$arr_page_link[4]];
                        $flt_height = isset($this->arr_page_sizes[$arr_link[0]]) ? $this->arr_page_sizes[$arr_link[0]][1] : $flt_page_height;
                        $str_annotations .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>', 1 + 2 * $arr_link[0],
                            $flt_height - $arr_link[1] * $this->flt_scale_factor);
                    }
                }
                $this->Out($str_annotations . ']');
            }
            if ($this->str_pdf_version > '1.3') {
                $this->Out('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
            }
            $this->Out('/Contents ' . ($this->int_current_object + 1) . ' 0 R>>');
            $this->Out('endobj');
            // Page content
            $str_page_content = ($this->bol_compress) ? gzcompress($this->arr_pages[$n]) : $this->arr_pages[$n];
            $this->NewObject();
            $this->Out('<<' . $str_filter . '/Length ' . strlen($str_page_content) . '>>');
            $this->PutStream($str_page_content);
            $this->Out('endobj');
        }
        // Pages root
        $this->arr_offsets[1] = strlen($this->str_buffer);
        $this->Out('1 0 obj');
        $this->Out('<</Type /Pages');
        $str_kids = '/Kids [';
        for ($i = 0; $i < $int_page; $i++) {
            $str_kids .= (3 + 2 * $i) . ' 0 R ';
        }
        $this->Out($str_kids . ']');
        $this->Out('/Count ' . $int_page);
        $this->Out(sprintf('/MediaBox [0 0 %.2F %.2F]', $flt_page_width, $flt_page_height));
        $this->Out('>>');
        $this->Out('endobj');
    }

    /**
     *
     */
    public function PutFonts()
    {
        $int_current_object = $this->int_current_object;
        foreach ($this->arr_encoding_diffs as $str_diff) {
            // Encodings
            $this->NewObject();
            $this->Out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $str_diff . ']>>');
            $this->Out('endobj');
        }
        foreach ($this->arr_font_files as $str_file => $arr_info) {
            if (!isset($arr_info['type']) || $arr_info['type'] != self::FONT_TRUETYPE) {
                // Font file embedding
                $this->NewObject();
                $this->arr_font_files[$str_file]['n'] = $this->int_current_object;
                $str_font = $this->readFontFile($this->getFontPath() . $str_file);

                $bol_compressed_file = (substr($str_file, -2) == '.z');
                if (!$bol_compressed_file && isset($arr_info['length2'])) {
                    $bol_header = (ord($str_font[0]) == 128);
                    if ($bol_header) {
                        // Strip first binary header
                        $str_font = substr($str_font, 6);
                    }
                    if ($bol_header && ord($str_font[$arr_info['length1']]) == 128) {
                        // Strip second binary header
                        $str_font = substr($str_font, 0, $arr_info['length1']) . substr($str_font, $arr_info['length1'] + 6);
                    }
                }
                $this->Out('<</Length ' . strlen($str_font));
                if ($bol_compressed_file) {
                    $this->Out('/Filter /FlateDecode');
                }
                $this->Out('/Length1 ' . $arr_info['length1']);
                if (isset($arr_info['length2'])) {
                    $this->Out('/Length2 ' . $arr_info['length2'] . ' /Length3 0');
                }
                $this->Out('>>');
                $this->PutStream($str_font);
                $this->Out('endobj');
            }
        }

        foreach ($this->arr_fonts as $str_key => $arr_font_data) {

            // Font objects
            //$this->fonts[$k]['n']=$this->n+1;
            $str_type = $arr_font_data['type'];
            $str_name = $arr_font_data['name'];
            if ($str_type == 'Core') {
                // Standard font
                $this->arr_fonts[$str_key]['n'] = $this->int_current_object + 1;
                $this->NewObject();
                $this->Out('<</Type /Font');
                $this->Out('/BaseFont /' . $str_name);
                $this->Out('/Subtype /Type1');
                if ($str_name != 'Symbol' && $str_name != 'ZapfDingbats') {
                    $this->Out('/Encoding /WinAnsiEncoding');
                }
                $this->Out('>>');
                $this->Out('endobj');
            } elseif ($str_type == 'Type1' || $str_type == 'TrueType') {
                // Additional Type1 or TrueType font
                $this->arr_fonts[$str_key]['n'] = $this->int_current_object + 1;
                $this->NewObject();
                $this->Out('<</Type /Font');
                $this->Out('/BaseFont /' . $str_name);
                $this->Out('/Subtype /' . $str_type);
                $this->Out('/FirstChar 32 /LastChar 255');
                $this->Out('/Widths ' . ($this->int_current_object + 1) . ' 0 R');
                $this->Out('/FontDescriptor ' . ($this->int_current_object + 2) . ' 0 R');
                if ($arr_font_data['enc']) {
                    if (isset($arr_font_data['diff'])) {
                        $this->Out('/Encoding ' . ($int_current_object + $arr_font_data['diff']) . ' 0 R');
                    } else {
                        $this->Out('/Encoding /WinAnsiEncoding');
                    }
                }
                $this->Out('>>');
                $this->Out('endobj');
                // Widths
                $this->NewObject();
                $int_character_width =& $arr_font_data['cw'];
                $str_data = '[';
                for ($i = 32; $i <= 255; $i++) {
                    $str_data .= $int_character_width[chr($i)] . ' ';
                }
                $this->Out($str_data . ']');
                $this->Out('endobj');
                // Descriptor
                $this->NewObject();
                $str_data = '<</Type /FontDescriptor /FontName /' . $str_name;
                foreach ($arr_font_data['desc'] as $str_inner_key => $str_value) {
                    $str_data .= ' /' . $str_inner_key . ' ' . $str_value;
                }
                $str_file = $arr_font_data['file'];
                if ($str_file) {
                    $str_data .= ' /FontFile' . ($str_type == 'Type1' ? '' : '2') . ' ' . $this->arr_font_files[$str_file]['n'] . ' 0 R';
                }
                $this->Out($str_data . '>>');
                $this->Out('endobj');
            } // TrueType embedded SUBSETS or FULL
            else {
                if ($str_type === self::FONT_TRUETYPE) {
                    $this->arr_fonts[$str_key]['n'] = $this->int_current_object + 1;

                    $obj_ttf = new TTFontFile();
                    $str_font_name = 'MPDFAA' . '+' . $arr_font_data['name'];
                    $str_subset = $arr_font_data['subset'];
                    unset($str_subset[0]);
                    $str_ttf_font_stream = $obj_ttf->makeSubset($arr_font_data['ttffile'], $str_subset);
                    $int_ttf_font_size = strlen($str_ttf_font_stream);
                    $str_font_stream_compressed = gzcompress($str_ttf_font_stream);
                    $str_code_to_glyph = $obj_ttf->getCodeToGlyph();
                    unset($str_code_to_glyph[0]);

                    // Type0 Font
                    // A composite font - a font composed of other fonts, organized hierarchically
                    $this->NewObject();
                    $this->Out('<</Type /Font');
                    $this->Out('/Subtype /Type0');
                    $this->Out('/BaseFont /' . $str_font_name . '');
                    $this->Out('/Encoding /Identity-H');
                    $this->Out('/DescendantFonts [' . ($this->int_current_object + 1) . ' 0 R]');
                    $this->Out('/ToUnicode ' . ($this->int_current_object + 2) . ' 0 R');
                    $this->Out('>>');
                    $this->Out('endobj');

                    // CIDFontType2
                    // A CIDFont whose glyph descriptions are based on TrueType font technology
                    $this->NewObject();
                    $this->Out('<</Type /Font');
                    $this->Out('/Subtype /CIDFontType2');
                    $this->Out('/BaseFont /' . $str_font_name . '');
                    $this->Out('/CIDSystemInfo ' . ($this->int_current_object + 2) . ' 0 R');
                    $this->Out('/FontDescriptor ' . ($this->int_current_object + 3) . ' 0 R');
                    if (isset($arr_font_data['desc']['MissingWidth'])) {
                        $this->Out('/DW ' . $arr_font_data['desc']['MissingWidth'] . '');
                    }

                    $this->PutTTFontWidths($arr_font_data, $obj_ttf->getMaxUni());

                    $this->Out('/CIDToGIDMap ' . ($this->int_current_object + 4) . ' 0 R');
                    $this->Out('>>');
                    $this->Out('endobj');

                    // ToUnicode
                    $this->NewObject();
                    $str_to_unicode = "/CIDInit /ProcSet findresource begin\n";
                    $str_to_unicode .= "12 dict begin\n";
                    $str_to_unicode .= "begincmap\n";
                    $str_to_unicode .= "/CIDSystemInfo\n";
                    $str_to_unicode .= "<</Registry (Adobe)\n";
                    $str_to_unicode .= "/Ordering (UCS)\n";
                    $str_to_unicode .= "/Supplement 0\n";
                    $str_to_unicode .= ">> def\n";
                    $str_to_unicode .= "/CMapName /Adobe-Identity-UCS def\n";
                    $str_to_unicode .= "/CMapType 2 def\n";
                    $str_to_unicode .= "1 begincodespacerange\n";
                    $str_to_unicode .= "<0000> <FFFF>\n";
                    $str_to_unicode .= "endcodespacerange\n";
                    $str_to_unicode .= "1 beginbfrange\n";
                    $str_to_unicode .= "<0000> <FFFF> <0000>\n";
                    $str_to_unicode .= "endbfrange\n";
                    $str_to_unicode .= "endcmap\n";
                    $str_to_unicode .= "CMapName currentdict /CMap defineresource pop\n";
                    $str_to_unicode .= "end\n";
                    $str_to_unicode .= "end";
                    $this->Out('<</Length ' . (strlen($str_to_unicode)) . '>>');
                    $this->PutStream($str_to_unicode);
                    $this->Out('endobj');

                    // CIDSystemInfo dictionary
                    $this->NewObject();
                    $this->Out('<</Registry (Adobe)');
                    $this->Out('/Ordering (UCS)');
                    $this->Out('/Supplement 0');
                    $this->Out('>>');
                    $this->Out('endobj');

                    // Font descriptor
                    $this->NewObject();
                    $this->Out('<</Type /FontDescriptor');
                    $this->Out('/FontName /' . $str_font_name);
                    foreach ($arr_font_data['desc'] as $str_inner_inner_key => $str_value) {
                        if ($str_inner_inner_key == 'Flags') {
                            $str_value = $str_value | 4;
                            $str_value = $str_value & ~32;
                        }   // SYMBOLIC font flag
                        $this->Out(' /' . $str_inner_inner_key . ' ' . $str_value);
                    }
                    $this->Out('/FontFile2 ' . ($this->int_current_object + 2) . ' 0 R');
                    $this->Out('>>');
                    $this->Out('endobj');

                    // Embed CIDToGIDMap
                    // A specification of the mapping from CIDs to glyph indices
                    $str_cid_to_gid_map = str_pad('', 256 * 256 * 2, "\x00");
                    foreach ($str_code_to_glyph as $cc => $str_clyph) {
                        $str_cid_to_gid_map[$cc * 2] = chr($str_clyph >> 8);
                        $str_cid_to_gid_map[$cc * 2 + 1] = chr($str_clyph & 0xFF);
                    }
                    $str_cid_to_gid_map = gzcompress($str_cid_to_gid_map);
                    $this->NewObject();
                    $this->Out('<</Length ' . strlen($str_cid_to_gid_map) . '');
                    $this->Out('/Filter /FlateDecode');
                    $this->Out('>>');
                    $this->PutStream($str_cid_to_gid_map);
                    $this->Out('endobj');

                    //Font file
                    $this->NewObject();
                    $this->Out('<</Length ' . strlen($str_font_stream_compressed));
                    $this->Out('/Filter /FlateDecode');
                    $this->Out('/Length1 ' . $int_ttf_font_size);
                    $this->Out('>>');
                    $this->PutStream($str_font_stream_compressed);
                    $this->Out('endobj');
                    unset($obj_ttf);
                } else {
                    // Allow for additional types
                    $this->arr_fonts[$str_key]['n'] = $this->int_current_object + 1;
                    $str_method = '_put' . strtolower($str_type);
                    if (!method_exists($this, $str_method)) {
                        throw new FPDFException('Unsupported font type: ' . $str_type, FPDFException::UNSUPPORTED_FONT);
                    }
                    $this->$str_method($arr_font_data);
                }
            }
        }
    }

    protected function PutTTFontWidths($font, $maxUni)
    {
        $cachePath = $this->getCachePath();
        $cacheFile = $cachePath . self::FILE_CHARACTER_WIDTH.$font['filename'].'.json';

        $rangeid = 0;
        $range = array();
        $prevcid = -2;
        $prevwidth = -1;
        $interval = false;
        $startcid = 1;

        if($cachePath !== null && file_exists($cacheFile)) {
            $fopen = fopen($cacheFile, 'r');
            $fread = fread($fopen, filesize($cacheFile));
            fclose($fopen);

            $json = json_decode($fread, true);

            if(json_last_error() !== JSON_ERROR_NONE) {
                extract($json);
                $startcid = 128;
            }
        }

        $cwlen = $maxUni + 1;
        // for each character
        for ($cid = $startcid; $cid < $cwlen; $cid++) {
            if ($font['cw'][$cid * 2] == "\00" && $font['cw'][$cid * 2 + 1] == "\00") {
                continue;
            }

            $width = (ord($font['cw'][$cid * 2]) << 8) + ord($font['cw'][$cid * 2 + 1]);
            if ($width == 65535) {
                $width = 0;
            }

            if ($cid > 255 && (!isset($font['subset'][$cid]) || !$font['subset'][$cid])) {
                continue;
            }

            if (!isset($font['dw']) || (isset($font['dw']) && $width != $font['dw'])) {
                if ($cid == ($prevcid + 1)) {
                    if ($width == $prevwidth) {
                        if ($width == $range[$rangeid][0]) {
                            $range[$rangeid][] = $width;
                        } else {
                            array_pop($range[$rangeid]);
                            // new range
                            $rangeid = $prevcid;
                            $range[$rangeid] = array();
                            $range[$rangeid][] = $prevwidth;
                            $range[$rangeid][] = $width;
                        }
                        $interval = true;
                        $range[$rangeid]['interval'] = true;
                    } else {
                        if ($interval) {
                            // new range
                            $rangeid = $cid;
                            $range[$rangeid] = array();
                            $range[$rangeid][] = $width;
                        } else {
                            $range[$rangeid][] = $width;
                        }
                        $interval = false;
                    }
                } else {
                    $rangeid = $cid;
                    $range[$rangeid] = array();
                    $range[$rangeid][] = $width;
                    $interval = false;
                }
                $prevcid = $cid;
                $prevwidth = $width;
            }
        }

    // write file
        if ($cachePath !== null && !file_exists($cacheFile)) {
            $fh = fopen($cacheFile, 'wb');

            $cw127 = [
                'rangeid'   => $rangeid,
                'prevcid'   => $prevcid,
                'prevwidth' => $prevwidth,
                'interval'  => (bool)$interval,
                'range'     => $range,
            ];

            fwrite($fh, json_encode($cw127));
            fclose($fh);
        }

        $prevk = -1;
        $nextk = -1;
        $prevint = false;
        foreach ($range as $k => $ws) {
            $cws = count($ws);
            if (($k == $nextk) AND (!$prevint) AND ((!isset($ws['interval'])) OR ($cws < 4))) {
                if (isset($range[$k]['interval'])) {
                    unset($range[$k]['interval']);
                }
                $range[$prevk] = array_merge($range[$prevk], $range[$k]);
                unset($range[$k]);
            } else {
                $prevk = $k;
            }
            $nextk = $k + $cws;
            if (isset($ws['interval'])) {
                if ($cws > 3) {
                    $prevint = true;
                } else {
                    $prevint = false;
                }
                unset($range[$k]['interval']);
                --$nextk;
            } else {
                $prevint = false;
            }
        }
        $w = '';
        foreach ($range as $k => $ws) {
            if (count(array_count_values($ws)) == 1) {
                $w .= ' ' . $k . ' ' . ($k + count($ws) - 1) . ' ' . $ws[0];
            } else {
                $w .= ' ' . $k . ' [ ' . implode(' ', $ws) . ' ]' . "\n";
            }
        }

        $this->Out('/W [' . $w . ' ]');
    }

    /**
     *
     */
    private function PutImages()
    {
        foreach (array_keys($this->arr_images) as $str_file) {
            $this->PutImage($this->arr_images[$str_file]);
            unset($this->arr_images[$str_file]['data']);
            unset($this->arr_images[$str_file]['smask']);
        }
    }

    /**
     * @param $arr_info
     */
    public function PutImage(&$arr_info)
    {
        $this->NewObject();
        $arr_info['n'] = $this->int_current_object;
        $this->Out('<</Type /XObject');
        $this->Out('/Subtype /Image');
        $this->Out('/Width ' . $arr_info['w']);
        $this->Out('/Height ' . $arr_info['h']);
        if ($arr_info['cs'] == 'Indexed') {
            $this->Out('/ColorSpace [/Indexed /DeviceRGB ' . (strlen($arr_info['pal']) / 3 - 1) . ' ' . ($this->int_current_object + 1) . ' 0 R]');
        } else {
            $this->Out('/ColorSpace /' . $arr_info['cs']);
            if ($arr_info['cs'] == 'DeviceCMYK') {
                $this->Out('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->Out('/BitsPerComponent ' . $arr_info['bpc']);
        if (isset($arr_info['f'])) {
            $this->Out('/Filter /' . $arr_info['f']);
        }
        if (isset($arr_info['dp'])) {
            $this->Out('/DecodeParms <<' . $arr_info['dp'] . '>>');
        }
        if (isset($arr_info['trns']) && is_array($arr_info['trns'])) {
            $trns = '';
            for ($i = 0; $i < count($arr_info['trns']); $i++) {
                $trns .= $arr_info['trns'][$i] . ' ' . $arr_info['trns'][$i] . ' ';
            }
            $this->Out('/Mask [' . $trns . ']');
        }
        if (isset($arr_info['smask'])) {
            $this->Out('/SMask ' . ($this->int_current_object + 1) . ' 0 R');
        }
        if(!array_key_exists("data",$arr_info)){$arr_info['data'] = '';}
        $this->Out('/Length ' . strlen($arr_info['data']) . '>>');
        $this->PutStream($arr_info['data']);
        $this->Out('endobj');
        // Soft mask
        if (isset($arr_info['smask'])) {
            $str_dp = '/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns ' . $arr_info['w'];
            $arr_smask = array(
                'w' => $arr_info['w'],
                'h' => $arr_info['h'],
                'cs' => 'DeviceGray',
                'bpc' => 8,
                'f' => $arr_info['f'],
                'dp' => $str_dp,
                'data' => $arr_info['smask']
            );
            $this->PutImage($arr_smask);
        }
        // Palette
        if ($arr_info['cs'] == 'Indexed') {
            $str_filter = ($this->bol_compress) ? '/Filter /FlateDecode ' : '';
            $str_pal = ($this->bol_compress) ? gzcompress($arr_info['pal']) : $arr_info['pal'];
            $this->NewObject();
            $this->Out('<<' . $str_filter . '/Length ' . strlen($str_pal) . '>>');
            $this->PutStream($str_pal);
            $this->Out('endobj');
        }
    }

    /**
     *
     */
    public function PutXObjectDict()
    {
        foreach ($this->arr_images as $arr_image) {
            $this->Out('/I' . $arr_image['i'] . ' ' . $arr_image['n'] . ' 0 R');
        }
    }

    /**
     *
     */
    public function PutResourceDict()
    {
        $this->Out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->Out('/Font <<');
        foreach ($this->arr_fonts as $arr_font) {
            $this->Out('/F' . $arr_font['i'] . ' ' . $arr_font['n'] . ' 0 R');
        }
        $this->Out('>>');
        $this->Out('/XObject <<');
        $this->PutXObjectDict();
        $this->Out('>>');
    }

    /**
     *
     */
    public function PutResources()
    {
        $this->PutFonts();
        $this->PutImages();
        // Resource dictionary
        $this->arr_offsets[2] = strlen($this->str_buffer);
        $this->Out('2 0 obj');
        $this->Out('<<');
        $this->PutResourceDict();
        $this->Out('>>');
        $this->Out('endobj');
		
		if(!empty($this->file_list)){
            $this->PutFiles();
		}
		if($this->str_color_profile_path){
			$this->PutColorProfile();
		}
        $this->PutXMP();
		
		
		if($this->Protection->isEncrypted()){
			
			$this->NewObject();
			$this->Protection->int_encryption_object_id = $this->int_current_object;
			$this->Out('<<');
//			$this->Protection->PutEncryption();
			$this->Out('/Filter /Standard');
			$this->Out('/V 1');
			$this->Out('/R 2');

			$this->Out('/O ('.$this->EscapeString($this->Protection->str_o_value).')');
			$this->Out('/U ('.$this->EscapeString($this->Protection->str_u_value).')');
			$this->Out('/P '.$this->Protection->int_p_value);
			$this->Out('>>');
			$this->Out('endobj');
		}
    }

    /**
     *
     */
    public function PutInfo()
    {
		foreach($this->metadata_list as $k => $v){
            $this->Out('/'.$k.' '.$this->TextString($v));
		}
		
//		$this->Out('/Producer ' . $this->TextString('csFPDF ' . $this->version));
		if (!empty($this->str_title)) {
            $this->Out('/Title ' . $this->TextString($this->str_title));
        }
        if (!empty($this->str_subject)) {
            $this->Out('/Subject ' . $this->TextString($this->str_subject));
        }
        if (!empty($this->str_author)) {
            $this->Out('/Author ' . $this->TextString($this->str_author));
        }
        if (!empty($this->str_keywords)) {
            $this->Out('/Keywords ' . $this->TextString($this->str_keywords));
        }
        if (!empty($this->str_creator)) {
            $this->Out('/Creator ' . $this->TextString($this->str_creator));
        }
        $this->Out('/CreationDate ' . $this->TextString('D:' . @date('YmdHis', $this->int_creation_date)));
    }

    /**
     *
     */
    public function PutCatalog()
    {
        $this->Out('/Type /Catalog');
        $this->Out('/Pages 1 0 R');
        if ($this->mix_zoom_mode == 'fullpage') {
            $this->Out('/OpenAction [3 0 R /Fit]');
        } elseif ($this->mix_zoom_mode == 'fullwidth') {
            $this->Out('/OpenAction [3 0 R /FitH null]');
        } elseif ($this->mix_zoom_mode == 'real') {
            $this->Out('/OpenAction [3 0 R /XYZ null null 1]');
        } elseif (!is_string($this->mix_zoom_mode)) {
            $this->Out('/OpenAction [3 0 R /XYZ null null ' . sprintf('%.2F', $this->mix_zoom_mode / 100) . ']');
        }
        if ($this->str_layout_mode == 'single') {
            $this->Out('/PageLayout /SinglePage');
        } elseif ($this->str_layout_mode == 'continuous') {
            $this->Out('/PageLayout /OneColumn');
        } elseif ($this->str_layout_mode == 'two') {
            $this->Out('/PageLayout /TwoColumnLeft');
        }
		
		if(!empty($this->file_list)){
			$a = [];
			foreach($this->file_list as $i => &$info){
				$a[] = $info["n_name"].' '.$info["n"].' 0 R';
			}
			$this->Out('/AF '.$this->file_index.' 0 R');
			$this->Out('/Names <</EmbeddedFiles <</Names ['.implode(' ',$a).']>>>>');
			
		}
		if($this->file_open_attachment_pane){
            $this->Out('/PageMode /UseAttachments');
		}
		if($this->str_color_profile_path){
			$this->Out('/OutputIntents [ '.$this->int_color_profile_desc.'  0 R ]');
		}
        $this->Out('/Metadata '.$this->int_metadata.' 0 R');
    }
	
	function PutFiles()
    {
//        $s = '';
        foreach($this->file_list as $i => &$info)
        {
			
			$info['other'] ??= [];
			$info['other']["relation"] ??= "Unspecified";
			$info['other']["subtype"] ??= "application/octet-stream";
			
            $file = $info['file'];
            $name = $info['name'];
            $desc = $info['desc'];
//            $relation = ($info['other']["relation"]??"Unspecified");
            $fc = $info['content'];
			$info['n'] = $this->int_current_object + 1;
			$info['n_name'] = '('.$this->EscapeString($name).')';
			
            $__ft = null;
//			$__fs = null;
			$checksum = false;
			if($fc === false){
				$fc = file_get_contents($file);
				if($fc===false){
					throw new FPDFException('Cannot open file `'.$file.'`', FPDFException::ATTACHMENT_INVALID_PATH);
				}
	            $__ft = filemtime($file);
//				$__fs = filesize($file);
				$checksum = md5_file($file);
			}
			if(!$__ft){
				$__ft = null;
			}
			if(!$checksum){
				$checksum = md5($fc);
			}
            $md = @date('YmdHis', $__ft);
//            $md = @date('YmdHis', filetime($file));
			
			$size = strlen($fc);
			$filesize = $size;
			if($this->bol_compress){
				$fc = gzcompress($fc);
				$size = strlen($fc);
			}
//			if($__fs){
//				$filesize = $__fs;
//			}
			
			

            $this->NewObject();
            $this->Out('<<');
            $this->Out('/F ('.$this->EscapeString($name).')');
			if($desc){
                $this->Out('/Desc '. $this->TextString($desc));
			}
            $this->Out('/Type /Filespec');
            $this->Out('/EF <<');
            $this->Out('/F '.($this->int_current_object+1).' 0 R');
            $this->Out('/UF '.($this->int_current_object+1).' 0 R');
            $this->Out('>>');
			$this->Out('/AFRelationship /'.$info['other']["relation"]);
			
            $this->Out('/UF '.$this->TextString(mb_convert_encoding($name, "UTF-8", mb_detect_encoding($name))));
            $this->Out('>>');
            $this->Out('endobj');

            $this->NewObject();
            $this->Out('<<');
			
            $this->Out('/Type /EmbeddedFile');
			$this->Out('/Subtype /' . str_replace("/", "#2f", $info['other']["subtype"]));
            $this->Out('/Length '.$size);
			if($this->bol_compress){
	            $this->Out('/Filter /FlateDecode');
			}
			
            $this->Out("/Params <<");
            $this->Out("/CheckSum (" . $checksum . ")");
            $this->Out("/ModDate (D:$md)");
            $this->Out("/Size ".$filesize."");
            $this->Out(">>");
            
			$this->Out('>>');
            $this->PutStream($fc);
            $this->Out('endobj');
        }
        $this->NewObject();
        $this->file_index = $this->int_current_object;
		
		
		$b = [];
		foreach($this->file_list as $i => $info2)
            $b[] = $info2['n'].' 0 R';
//		
		
//        $this->Out('<<');
		$this->Out('['.implode(' ',$b).']');
        $this->Out('endobj');
    }
	
	
	protected function PutColorProfile()
    {
        $icc = file_get_contents($this->str_color_profile_path);
		if(!$icc){
			throw new FPDFException('Could not load the color profile `'.$this->str_color_profile_path.'`', FPDFException::INVALID_COLOR_PROFILE_PATH);
		}
		$icc_name = basename($this->str_color_profile_path);
		
        $this->NewObject();
        $this->int_color_profile = $this->int_current_object;
        $this->Out('<<');
        $this->Out('/Length '.strlen($icc));
        $this->Out('/N 3');
        $this->Out('>>');
        $this->PutStream($icc);
        $this->Out('endobj');
		
		
        $this->NewObject();
        $this->int_color_profile_desc = $this->int_current_object;
        $this->Out('<<');
		
		$this->Out('/DestOutputProfile '.$this->int_color_profile.' 0 R');
        $this->Out('/OutputConditionIdentifier (' . $icc_name . ') /Info (' . $icc_name . ') /RegistryName ( http://www.color.org )');
        $this->Out('/Type /OutputIntent');
        $this->Out('/S /GTS_PDFA1');
//        $this->Out('/Length '.strlen($icc));
//        $this->Out('/N 3');
        $this->Out('>>');
        $this->Out('endobj');
    }

    function PutXMP()
    {
		if($this->b_xmp_out){
			return;
		}
		if ($this->int_state == self::DOCUMENT_STATE_NOT_INITIALIZED) {
            $this->Open();
        }
		
		$this->b_xmp_out = true;
		if($this->str_xmp){
			
			$s = str_ireplace('<?xml version="1.0"?>', '', $this->str_xmp);
			
			
			$size = strlen($s);
			if($this->bol_compress){
				$s = gzcompress($s);
				$size = strlen($s);
			}
			
			$this->NewObject();
			$this->int_metadata = $this->int_current_object;
			$this->Out('<<');
			if($this->bol_compress){
	            $this->Out('/Filter /FlateDecode');
			}
			$this->Out('/Type /Metadata');
			$this->Out('/Subtype /XML');
			$this->Out('/Length '.$size);
			$this->Out('>>');
			$this->PutStream($s);
			$this->Out('endobj');
			
			return;
		}
        $pdf = $this->GetXmpSimple('pdf:Producer', $this->metadata_list['Producer']);
		if($this->str_keywords){
            $pdf .= $this->GetXmpSimple('pdf:Keywords', $this->str_keywords);
		}

        $date = @date('c', $this->int_creation_date);
        $xmp = $this->GetXmpSimple('xmp:CreateDate', $date);
		if($this->str_creator){
            $xmp .= $this->GetXmpSimple('xmp:CreatorTool', $this->str_creator);
		}

        $dc = '';
		if($this->str_author){
            $dc .= $this->GetXmpSeq('dc:creator', $this->str_author);
		}
		if($this->str_title){
            $dc .= $this->GetXmpAlt('dc:title', $this->str_title);
		}
		if($this->str_subject){
            $dc .= $this->GetXmpAlt('dc:description', $this->str_subject);
		}

//        $pdfaid = $this->GetXmpSimple('pdfaid:part', '1');
//        $pdfaid .= $this->GetXmpSimple('pdfaid:conformance', 'B');
        $pdfaid = $this->GetXmpSimple('pdfaid:part', '3');
        $pdfaid .= $this->GetXmpSimple('pdfaid:conformance', 'B');

        $s = '<?xpacket begin="" id="W5M0MpCehiHzreSzNTczkc9d"?>'."\n";
        $s .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n";
        $s .= $this->GetXmpDescription('pdf', 'http://ns.adobe.com/pdf/1.3/', $pdf);
        $s .= $this->GetXmpDescription('xmp', 'http://ns.adobe.com/xap/1.0/', $xmp);
		if($dc){
            $s .= $this->GetXmpDescription('dc', 'http://purl.org/dc/elements/1.1/', $dc);
		}
        $s .= $this->GetXmpDescription('pdfaid', 'http://www.aiim.org/pdfa/ns/id/', $pdfaid);
        $s .= '</rdf:RDF>'."\n";
        $s .= '<?xpacket end="r"?>';

        $this->NewObject();
        $this->int_metadata = $this->int_current_object;
        $this->Out('<<');
        $this->Out('/Type /Metadata');
        $this->Out('/Subtype /XML');
        $this->Out('/Length '.strlen($s));
        $this->Out('>>');
        $this->PutStream($s);
        $this->Out('endobj');
    }

    /**
     *
     */
    public function PutHeader()
    {
        $this->Out('%PDF-' . $this->str_pdf_version);
		//This sequence is a kind of “signature” that helps to identify the file as a valid PDF and avoid reading errors.
		$this->Out("%\xE2\xE3\xCF\xD3");
    }

    /**
     *
     */
    public function PutTrailer()
    {
        $this->Out('/Size ' . ($this->int_current_object + 1));
        $this->Out('/Root ' . $this->int_current_object . ' 0 R');
        $this->Out('/Info ' . ($this->int_current_object - 1) . ' 0 R');
        $this->Out('/ID [(' . $this->str_id . ')(' . $this->str_id . ')]');
		
		
		if($this->Protection->isEncrypted()){
			
			$this->Out('/Encrypt ' . $this->Protection->int_encryption_object_id . ' 0 R');
			$this->Out('/ID [()()]');
		}
		
    }

    /**
     *
     */
    public function EndDoc()
    {
        $this->int_creation_date = time();
		
        $this->PutHeader();
        $this->PutPages();
        $this->PutResources();
        // Info
        $this->NewObject();
        $this->Out('<<');
        $this->PutInfo();
        $this->Out('>>');
        $this->Out('endobj');
        // Catalog
        $this->NewObject();
        $this->Out('<<');
        $this->PutCatalog();
        $this->Out('>>');
        $this->Out('endobj');
        // Cross-ref
        $int_buffer_size = strlen($this->str_buffer);
        $this->Out('xref');
        $this->Out('0 ' . ($this->int_current_object + 1));
        $this->Out('0000000000 65535 f ');
        for ($i = 1; $i <= $this->int_current_object; $i++) {
            $this->Out(sprintf('%010d 00000 n ', $this->arr_offsets[$i]));
        }
        // Trailer
        $this->Out('trailer');
        $this->Out('<<');
        $this->PutTrailer();
        $this->Out('>>');
        $this->Out('startxref');
        $this->Out($int_buffer_size);
        $this->Out('%%EOF');
        $this->int_state = self::DOCUMENT_STATE_TERMINATED;
    }

    /**
     * Converts UTF-8 strings to UTF16-BE
     *
     * @param string $str_input
     * @param bool $bol_set_byte_order_mark
     * @return string
     */
    public function UTF8ToUTF16BE($str_input, $bol_set_byte_order_mark = true)
    {
        $str_output = "";
        if ($bol_set_byte_order_mark) {
            $str_output .= "\xFE\xFF";
        }
        $str_output .= mb_convert_encoding($str_input, 'UTF-16BE', 'UTF-8');
        return $str_output;
    }

    /**
     * Converts UTF-8 strings to codepoints array
     *
     * @param string $str_input
     * @return array
     */
    public function UTF8StringToArray($str_input)
    {
		//Force cast as string, can sometimes crash with some PHP version
		if(is_numeric($str_input)){
			$str_input = (string)$str_input;
		}
        $arr_output = array();
        $int_string_length = strlen($str_input);
        for ($i = 0; $i < $int_string_length; $i++) {
            $uni = -1;
            $int_char = ord($str_input[$i]);
            if ($int_char <= 0x7F) {
                $uni = $int_char;
            } elseif ($int_char >= 0xC2) {
                if (($int_char <= 0xDF) && ($i < $int_string_length - 1)) {
                    $uni = ($int_char & 0x1F) << 6 | (ord($str_input[++$i]) & 0x3F);
                } elseif (($int_char <= 0xEF) && ($i < $int_string_length - 2)) {
                    $uni = ($int_char & 0x0F) << 12 | (ord($str_input[++$i]) & 0x3F) << 6
                        | (ord($str_input[++$i]) & 0x3F);
                } elseif (($int_char <= 0xF4) && ($i < $int_string_length - 3)) {
                    $uni = ($int_char & 0x0F) << 18 | (ord($str_input[++$i]) & 0x3F) << 12
                        | (ord($str_input[++$i]) & 0x3F) << 6
                        | (ord($str_input[++$i]) & 0x3F);
                }
            }
            if ($uni >= 0) {
                $arr_output[] = $uni;
            }
        }
        return $arr_output;
    }

    /**
     * With this method you can set the cache path
     *
     * @param string $cachePath The cache folder
     *
     * @return string The newly set cache folder
     */
    public function setCachePath(?string $cachePath = null) {
        if(!file_exists($cachePath)) {
            @mkdir($cachePath, 0775, true);
        }

        if(!file_exists($cachePath) || !is_dir($cachePath) || !is_writable($cachePath)) {
            throw new FPDFException('Could not write to cache folder: '.$cachePath, FPDFException::INVALID_CACHE_FOLDER);
        }

        $this->cachePath = realpath($cachePath).'/';

        return $cachePath;
    }

    /**
     * Get the currently set cache folder
     *
     * @return string The cache folder, may be null
     */
    public function getCachePath() {
       return $this->cachePath;
    }

    /**
     * Clear all cached files in the cache folder
     *
     */
    public function clearCache() {
        $cachePath = $this->getCachePath();
        if($cachePath !== null) {
            $cacheFiles = glob($cachePath.'*.*');
            foreach($cacheFiles as $cacheFile) {
                @unlink($cacheFile);
            }
        }
    }

    /**
     * Calculates the expected line height for a multi cell,
     *      useful if you want to know how much space a cell is going to use
     *
     * @param string $text The text to calculate the line height from
     * @param int $cellWidth The width of the multicell
     * @param int $lineHeight The line height of the multicell
     *
     * @return int The calculated line height
     */
    public function calculateHeight(string $text, int $cellWidth = 80, int $lineHeight = 3): int {
        $explode = explode("\r", $text);

        array_walk($explode, 'trim');

        $lines = [];
        foreach($explode as $split) {
            $sub = $split;
            $char = 1;

            while($char <= strlen($sub)) {
                $substr = substr($sub, 0, $char);

                if($this->getStringWidth($substr) >= $cellWidth - 1) {
                    $pos = strrpos($substr, " ");

                    $lines[] = substr($sub, 0, ($pos !== FALSE ? $pos : $char)).($pos === FALSE ? '-' : '');

                    if($pos !== FALSE) { //if $pos returns FALSE, substr has no whitespace, so split word on current position
                        $char = $pos + 1;
                        $len = $char;
                    }

                    $sub = ltrim(substr($sub, $char));
                    $char = 0;
                }

                $char++;
            }

            if(!empty($sub)) {
                $lines[] = $sub;
            }
        }

        return (int)count($lines) * $lineHeight;
    }

    /**
     * Change the number of the current page
     */
    public function setPage(int $page) {
		$this->int_page = $page;

		return $this;
    }

    /**
     * Get page array
     */
    public function getPageArray(): array {
        return $this->arr_pages;
    }

    /**
     * Get page count
     */
    public function getPageCount(): int {
        return (int)count($this->arr_pages);
    }
	
	
	
	//** ********************************* **
	//** ****** FPDF -> Attachments ****** **
	//** ********************************* **
	function file_Attach($file, $name='', $desc='', $isUTF8=false, $other = []){
        if($name == ''){
            $p = strrpos($file,'/');
            if($p === false){
                $p = strrpos($file,'\\');
			}
			if($p !== false){
                $name = substr($file, $p+1);
			}else{
				$name = $file;
			}
        }
        if(!$isUTF8){
            $desc = mb_convert_encoding($desc, "UTF-8", mb_detect_encoding($desc));
		}
        $this->file_list[] = ['file' => $file, 'name' => $name, 'desc' => $desc, "content" => false, "other" => $other];
    }
	
	function file_AttachContent($file, $name='', $desc='', $content = '', $isUTF8=false, $other = []){
        if($name == ''){
            $p = strrpos($file,'/');
            if($p === false){
                $p = strrpos($file,'\\');
			}
			if($p !== false){
                $name = substr($file, $p+1);
			}else{
				$name = $file;
			}
        }
        if(!$isUTF8){
            $desc = mb_convert_encoding($desc, "UTF-8", mb_detect_encoding($desc));
            $content = mb_convert_encoding($content, "UTF-8", mb_detect_encoding($content));
		}
		$tmpfile_path = tempnam(sys_get_temp_dir(), 'CS_FPDF_');
		file_put_contents($tmpfile_path, $content);
		
        $this->file_list[] = ['file' => $tmpfile_path, 'name' => $name, 'desc' => $desc, "content" => false, "other" => $other];
    }
	
	function file_OpenAttachmentPane(bool $bOpen = true){
        $this->file_open_attachment_pane = $bOpen;
    }

    /**
     * Set the color profile path
     *
     * @param $colorProfilePath The color profile file path
     */
    public function setColorProfilePath(string $colorProfilePath): string {
		$str_color_profile_path = realpath($colorProfilePath);
        if(!file_exists($str_color_profile_path) || is_dir($str_color_profile_path)) {
            throw new FPDFException('Color profile file does not exist `'.$str_color_profile_path.'`', FPDFException::INVALID_COLOR_PROFILE_PATH);
        }

        $this->str_color_profile_path = $str_color_profile_path;

        return $this->str_color_profile_path;
    }

    /**
     * @return string
     */
    protected function getColorProfilePath()
    {
        return realpath($this->str_color_profile_path).'/';
    }
	
	//** ***************************** **
	//** ****** PDF/A FUNCTIONS ****** **
	//** ***************************** **
	function GetXmpDescription($alias, $ns, $body)
    {
        return sprintf("\t<rdf:Description rdf:about=\"\" xmlns:%s=\"%s\">\n%s\t</rdf:Description>\n", $alias, $ns, $body);
    }

    function GetXmpSimple($tag, $value)
    {
        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
        return sprintf("\t\t<%s>%s</%s>\n", $tag, $value, $tag);
    }

    function GetXmpSeq($tag, $value)
    {
        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
        return sprintf("\t\t<%s>\n\t\t\t<rdf:Seq>\n\t\t\t\t<rdf:li>%s</rdf:li>\n\t\t\t</rdf:Seq>\n\t\t</%s>\n", $tag, $value, $tag);
    }

    function GetXmpAlt($tag, $value)
    {
        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
        return sprintf("\t\t<%s>\n\t\t\t<rdf:Alt>\n\t\t\t\t<rdf:li xml:lang=\"x-default\">%s</rdf:li>\n\t\t\t</rdf:Alt>\n\t\t</%s>\n", $tag, $value, $tag);
    }
}
