<?php
namespace Jamm\GoogleCloudPrint;

class Ticket
{
  private $version = '1.0';
  private $color = 0;
  private $page_orientation = 0;
  private $copies = 1;
  private $dpi_horizontal = null;
  private $dpi_vertical = null;
  private $fit_to_page = null;
  private $page_range_intervals = null;
  private $media_size_width = null;
  private $media_size_height = null;
  private $media_size_continuous_feed = null;
  private $collate = false;
  private $reverse_order = false;
  private $vendor_ticket_items = [];

  /**
   * @param boolean $collate
   */
  public function setCollate($collate)
  {
    $this->collate = $collate;
  }

  /**
   * @param int $color
   *  0 - color
   *  1 - monochrome
   */
  public function setColor($color)
  {
    $this->color = $color;
  }

  /**
   * @param int $copies
   */
  public function setCopies($copies)
  {
    $this->copies = $copies;
  }

  /**
   * @param int $dpi_horizontal
   */
  public function setDpiHorizontal($dpi_horizontal)
  {
    $this->dpi_horizontal = $dpi_horizontal;
  }

  /**
   * @param int $dpi_vertical
   */
  public function setDpiVertical($dpi_vertical)
  {
    $this->dpi_vertical = $dpi_vertical;
  }

  /**
   * @param int $fit_to_page
   *  0  - No Fitting
   *  1  - Shrink to Page
   *  2  - Grow to Page
   *  3  - Fit to Page
   *  4  - Fill Page
   */
  public function setFitToPage($fit_to_page)
  {
    $this->fit_to_page = $fit_to_page;
  }

  /**
   * @param boolean $media_size_continuous_feed
   */
  public function setMediaSizeContinuousFeed($media_size_continuous_feed)
  {
    $this->media_size_continuous_feed = $media_size_continuous_feed;
  }

  /**
   * @param int $media_size_height (in microns)
   */
  public function setMediaSizeHeight($media_size_height = 297000)
  {
    $this->media_size_height = $media_size_height;
  }

  /**
   * @param int $media_size_width (in microns)
   */
  public function setMediaSizeWidth($media_size_width = 210000)
  {
    $this->media_size_width = $media_size_width;
  }

  /**
   * @param int $page_orientation
   */
  public function setPageOrientation($page_orientation)
  {
    $this->page_orientation = $page_orientation;
  }

  /**
   * @param [][] $page_range_intervals array of arrays[start, end]
   */
  public function setPageRangeIntervals($page_range_intervals)
  {
    $this->page_range_intervals = $page_range_intervals;
  }

  /**
   * @param boolean $reverse_order
   */
  public function setReverseOrder($reverse_order)
  {
    $this->reverse_order = $reverse_order;
  }

  /**
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }

  public function addVendorTicketItem($id, $value)
  {
    $this->vendor_ticket_items[] = ['id' => $id, 'value' => $value];
  }

  public function setVendorTicketItems(array $value)
  {
    $this->vendor_ticket_items = $value;
  }

  public function __toString()
  {
    return json_encode($this->getArray());
  }

  public function getArray()
  {
    $a                         = ['version' => $this->version];
    $print['color']            = ['vendor_id' => '', 'type' => $this->color];
    $print['page_orientation'] = ['type' => $this->page_orientation];
    $print['copies']           = ['copies' => $this->copies];
    if ($this->dpi_horizontal || $this->dpi_vertical) {
      $print['dpi'] = ['vendor_id' => ''];
      if ($this->dpi_horizontal) {
        $print['dpi']['horizontal_dpi'] = $this->dpi_horizontal;
      }
      if ($this->dpi_vertical) {
        $print['dpi']['vertical_dpi'] = $this->dpi_vertical;
      }
    }
    if ($this->fit_to_page) {
      $print['fit_to_page'] = ['type' => $this->fit_to_page];
    }
    if (!empty($this->page_range_intervals)) {
      $intervals = [];
      foreach ($this->page_range_intervals as $interval) {
        $intervals[] = ['start' => $interval[0], 'end' => $interval[1]];
      }
      if (!empty($intervals)) {
        $print['page_range'] = ['interval' => $intervals];
      }
    }
    if ($this->media_size_width || $this->media_size_height || $this->media_size_continuous_feed) {
      $print['media_size'] = ['vendor_id' => ''];
      if ($this->media_size_width) {
        $print['media_size']['width_microns'] = $this->media_size_width;
      }
      if ($this->media_size_height) {
        $print['media_size']['height_microns'] = $this->media_size_height;
      }
      if ($this->media_size_continuous_feed) {
        $print['media_size']['is_continuous_feed'] = $this->media_size_continuous_feed;
      }
    }
    if ($this->collate) {
      $print['collate'] = ['collate' => $this->collate];
    }
    if ($this->reverse_order) {
      $print['reverse_order'] = ['reverse_order' => $this->reverse_order];
    }
    if (!empty($this->vendor_ticket_items)) {
      $print['vendor_ticket_item'] = $this->vendor_ticket_items;
    }
    $a['print'] = $print;
    return $a;
  }
}
