<?php

namespace CS\FpdfBundle\Exception;

use \Exception;

class FPDFException extends Exception {

// Invalid unit specified
	const INVALID_UNIT = -1;

// Invalid orientiation specified (must be portrait or landscape)
	const INVALID_ORIENTATION = -2;

// Invalid zoom mode specified
	const INVALID_ZOOM_MODE = -3;

// Invalid layout mode specified
	const INVALID_LAYOUT_MODE = -4;

// Invalid page size is specified
	const INVALID_PAGE_SIZE = -5;

// Invalid font specified
	const UNDEFINED_FONT = -6;

// Unsupported font specified, the font you are trying to use is not loaded
	const UNSUPPORTED_FONT = -7;

// Invalid font file
	const INVALID_FONT_FILE = -8;

// Invalid font specified
	const INVALID_FONT_PATH = -9;

// Invalid image was specified, not existing or not readable
	const INVALID_IMAGE = -10;

// Unsupported image was specified
	const UNSUPPORTED_IMAGE = -11;

// Image not writable, probally temp directory not writable
	const IMAGE_NOT_WRITABLE = -12;

// Headers have already been send
	const HEADER_ALREADY_SENT = -13;

// Cache folder is invalid or not writable
	const INVALID_CACHE_FOLDER = -14;

// A required PHP extension is not available
	const EXTENSION_NOT_AVAILABLE = -15;

// Stream could not be readed (completely)
	const INVALID_STREAM = -16;

// Color profile path is invalid or not readable
	const INVALID_COLOR_PROFILE_PATH = -17;

// Attachment path is invalid or not readable
	const ATTACHMENT_INVALID_PATH = -18;

// Output path is invalid or not readable
	const OUTPUT_INVALID_PATH = -19;

// Output destination type is not valid
	const OUTPUT_INVALID_DESTINATION = -20;

// Digit check for EAN13 code failed
	const BARCODE_INCORECT_DIGIT_CHECK = -21;

// Character checks for CODE39 code failed
	const BARCODE_39_INVALID_VALUE = -22;

}