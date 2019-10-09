<?php
/**
 * Zira project.
 * image.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;

class Image extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-picture';
    protected static $_title = 'Image editor';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Reload'), 'glyphicon glyphicon-repeat', 'desk_call(dash_image_reload, this);', 'reload', true)
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButtonGroup(array(
                $this->createToolbarButton(null, Zira\Locale::t('Zoom In'), 'glyphicon glyphicon-zoom-in', 'desk_call(dash_image_zoom_in, this);', 'zoom', true),
                $this->createToolbarButton(null, Zira\Locale::t('Zoom Out'), 'glyphicon glyphicon-zoom-out', 'desk_call(dash_image_zoom_out, this);', 'zoom', true),
            ))
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButtonGroup(array(
                $this->createToolbarButton(null, Zira\Locale::t('Crop'), 'glyphicon glyphicon-resize-full', 'desk_call(dash_image_crop, this);', 'crop', true),
                $this->createToolbarButton(null, Zira\Locale::t('Cut'), 'glyphicon glyphicon-scissors', 'desk_call(dash_image_cut, this);', 'cut', true),
            ))
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButtonGroup(array(
                $this->createToolbarButton('[&nbsp;:&nbsp;]', Zira\Locale::t('Aspect ratio'), null, 'desk_call(dash_image_aspect_ratio, this, element);', 'ratio', true, false, array('typo'=>'0_0')),
                $this->createToolbarButton('[1:1]', Zira\Locale::t('Aspect ratio'), null, 'desk_call(dash_image_aspect_ratio, this, element);', 'ratio', true, false, array('typo'=>'1_1')),
                $this->createToolbarButton('[4:3]', Zira\Locale::t('Aspect ratio'), null, 'desk_call(dash_image_aspect_ratio, this, element);', 'ratio', true, false, array('typo'=>'4_3')),
                $this->createToolbarButton('[16:9]', Zira\Locale::t('Aspect ratio'), null, 'desk_call(dash_image_aspect_ratio, this, element);', 'ratio', true, false, array('typo'=>'16_9')),
            ))
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Save'), Zira\Locale::t('Save'), 'glyphicon glyphicon-floppy-disk', 'desk_window_save(this);', 'save', true, true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Save'), 'glyphicon glyphicon-floppy-disk', 'desk_window_save(this);', 'save', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Save as'), 'glyphicon glyphicon-floppy-open', 'desk_call(dash_image_save_as, this);', 'save', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Change width'), 'glyphicon glyphicon-resize-horizontal', 'desk_call(dash_image_change_width, this);', 'resize', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Change height'), 'glyphicon glyphicon-resize-vertical', 'desk_call(dash_image_change_height, this);', 'resize', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Crop width'), 'glyphicon glyphicon-object-align-right', 'desk_call(dash_image_crop_width, this);', 'crop', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Crop height'), 'glyphicon glyphicon-object-align-bottom', 'desk_call(dash_image_crop_height, this);', 'crop', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Add watermark'), 'glyphicon glyphicon-registration-mark', 'desk_call(dash_image_watermark, this);', 'watermark', true)
        );
        // image qty menu
        $jpeg_quality = Zira\Image::QUALITY_JPEG;
        $qtyMenu = array();
        for ($i=50; $i<=$jpeg_quality; $i+=5) {
            $qtyMenu []= $this->createMenuDropdownItem($i.'%', 'glyphicon glyphicon-unchecked', 'desk_call(dash_image_change_quality, this, element);', 'quality', true, array('qty' => $i));
        }
        $menu = array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Quality'), $qtyMenu)
        );
        $this->setMenuItems($menu);
        
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Save'), 'glyphicon glyphicon-floppy-disk', 'desk_window_save(this);', 'save', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Save as'), 'glyphicon glyphicon-floppy-open', 'desk_call(dash_image_save_as, this);', 'save', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Change width'), 'glyphicon glyphicon-resize-horizontal', 'desk_call(dash_image_change_width, this);', 'resize', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Change height'), 'glyphicon glyphicon-resize-vertical', 'desk_call(dash_image_change_height, this);', 'resize', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Crop width'), 'glyphicon glyphicon-object-align-right', 'desk_call(dash_image_crop_width, this);', 'crop', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Crop height'), 'glyphicon glyphicon-object-align-bottom', 'desk_call(dash_image_crop_height, this);', 'crop', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Add watermark'), 'glyphicon glyphicon-registration-mark', 'desk_call(dash_image_watermark, this);', 'watermark', true)
        );
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_image_open, this);'
            )
        );
        $this->setOnCloseJSCallback(
            $this->createJSCallback(
                'desk_call(dash_image_close, this);'
            )
        );
        $this->setOnResizeJSCallback(
            $this->createJSCallback(
                'desk_call(dash_image_resize, this);'
            )
        );
        $this->setOnFocusJSCallback(
            $this->createJSCallback(
                'desk_call(dash_image_focus, this);'
            )
        );
        $this->setOnBlurJSCallback(
            $this->createJSCallback(
                'desk_call(dash_image_blur, this);'
            )
        );
        $this->setOnSaveJSCallback(
            $this->createJSCallback(
                'desk_call(dash_image_save, this);'
            )
        );

        $this->addStrings(array(
            'Replace image ?',
            'Image width',
            'Image height',
            'Enter name'
        ));

        $this->addVariables(array(
            'dash_image_files_wnd' => Dash::getInstance()->getWindowJSName(Files::getClass()),
            'dash_image_wnd' => $this->getJSClassName()
        ));
        
        $this->addVariables(array(
            'dash_image_jpeg_quality' => $jpeg_quality
        ), true);

        $this->includeJS('dash/image');
    }
}