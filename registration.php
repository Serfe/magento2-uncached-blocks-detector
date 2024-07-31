<?php
/**
 * This file is part of Serfe/UncacheableBlocksDetector which is released under GNU General Public License
 * See COPYING.txt for license details.
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Serfe_UncacheableBlockDetector',
    __DIR__
);
