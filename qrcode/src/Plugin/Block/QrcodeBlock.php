<?php

namespace Drupal\Qrcode\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

/**
 * Provides a 'Qrcode for products' block.
 *
 * @Block(
 *   id = "Qrcode_block",
 *   admin_label = @Translation("Qrcodecomponent"),
 *   category = @Translation("Qrcode"),
 * )
 */
class QrcodeBlock extends BlockBase {

  /**
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }

  function build()
  {
    $node = \Drupal::routeMatch()->getParameter('node');
    if(!empty($node))
    {
       $nodeType =  $node->bundle();
        if ($nodeType === 'products' && $node->hasField('field_product_link'))
        {       
          $link = $node->get('field_product_link')->getValue()[0]['uri'];
        }
    }
    $writer = new PngWriter();
    // Create QR code
    $qrCode = QrCode::create($link)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
        ->setSize(300)
        ->setMargin(10)
        ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));
    $result = $writer->write($qrCode);
    $dataUri = $result->getDataUri();
    $mark_up = "<img src='$dataUri' />";

    return [
      '#markup' => $this->t($mark_up),
    ];
  }
 
}

  