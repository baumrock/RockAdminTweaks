<?php

namespace RockAdminTweaks;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class ColumnBreak extends Tweak
{

    public $editedPage = false;

    public function info(): array
    {
        return [
        'description' => 'Add aos_column_break field to create admin columns (compatible with AOS version)',
        ];
    }

    public function ready(): void
    {
        // use skipLabel and collapsed to avoid visiblity if tweak is uninstalled (no need to remove from templates)
        if (!$this->wire()->fields->get('aos_column_break')) {

            $field = new \ProcessWire\Field();
            $field->type = $this->wire('modules')->get('FieldtypeText');
            $field->name = 'aos_column_break';
            $field->label = '';
            $field->skipLabel = true;
            $field->collapsed = \ProcessWire\Inputfield::collapsedYesLocked;
            $field->tags = '-aos';
            $field->save();

            $this->message(\ProcessWire\__('Installed field "aos_column_break".', __FILE__));
        }

        $this->wire('config')->scripts->add($this->pathToUrl(__DIR__ . '/split.js/split.min.js'));

        $editedPageId = $this->wire('sanitizer')->int($this->wire('input')->get->id);
        $editedPage = $this->wire('pages')->get($editedPageId);

        if ($editedPage->id && !($editedPage instanceof RepeaterPage)) {
            $this->editedPage = $editedPage;
        }

        $this->addHookAfter('ProcessPageEdit::buildFormContent', $this, 'setupAdminColumns');

        $this->loadCSS();
        $this->loadJS();
    }

    public function setupAdminColumns($event)
    {
        $form = $event->return;
        $fields = $form->children();
        $colBreakField = $fields->get('aos_column_break');
        $colWidths = array(67, 33);
        $tabOpenFields = $fields->find('hasFieldtype=FieldtypeFieldsetTabOpen');

        if ($tabOpenFields->count()) {
            $this->setupAdminColumnsTabs($tabOpenFields, $form);
        }

        if (!$colBreakField) {
            return false;
        }

        // stop if colBreakField is inside a tab
        $tabSeen = false;

        foreach ($fields as $field) {

            if ($field->hasFieldtype == 'FieldtypeFieldsetTabOpen') {
                $tabSeen = true;
            }

            if ($field->name == $colBreakField->name) {

                if ($tabSeen) {
                    // there was a TabOpen field first, remove colBreakField and stop
                    $form->remove($colBreakField);

                    return false;

                } else {
                    // colBreakField is not inside a tab
                    break;
                }
            }
        }

        if ($colBreakField->columnWidth) {
            $colWidths = array($colBreakField->columnWidth, 100 - $colBreakField->columnWidth);
        }

        $fsetLeft = $this->wire('modules')->get('InputfieldFieldset');
        $fsetLeft->attr('class', $fsetLeft->attr('class') . ' aos_col_left aos_no-inputfield-padding');
        $fsetLeft->set('themeBorder', 'none');
        $fsetLeft->set('themeOffset', false);
        $fsetLeft->wrapAttr('style', 'width: ' . $colWidths[0] . '%');
        $fsetLeft->wrapAttr('data-splitter-default', $colWidths[0]);

        $fsetRight = $this->wire('modules')->get('InputfieldFieldset');
        $fsetRight->set('themeBorder', 'none');
        $fsetRight->set('themeOffset', false);
        $fsetRight->attr('class', $fsetRight->attr('class') . ' aos_col_right aos_no-inputfield-padding');
        $fsetRight->wrapAttr('style', 'width: ' . $colWidths[1] . '%');
        $fsetRight->wrapAttr('data-splitter-default', $colWidths[1]);

        // add template name and user id for Split.js
        $fsetRight->wrapAttr('data-splitter-storagekey',
            'splitter_' . $this->editedPage->template->name . '_' . $this->wire('user')->id);

        $this->wire('modules')->get('FieldtypeFieldsetClose');
        $fsetLeftEnd = new \ProcessWire\InputfieldFieldsetClose;
        $fsetRightEnd = new \ProcessWire\InputfieldFieldsetClose;

        $fsetLeftEnd->name = 'aos_col_left' . \ProcessWire\FieldtypeFieldsetOpen::fieldsetCloseIdentifier;
        $fsetRightEnd->name = 'aos_col_right' . \ProcessWire\FieldtypeFieldsetOpen::fieldsetCloseIdentifier;

        $fset = $fsetLeft;
        $rightItems = false;

        foreach ($fields as $f) {

            // stop on first Tab field
            if ($f->hasFieldtype == 'FieldtypeFieldsetTabOpen') {
                break;
            }

            // if colBreakField reached, remove it and start adding fields to the right column
            if (!$rightItems && $f == $colBreakField) {
                $form->remove($colBreakField);
                $fset = $fsetRight;
                $rightItems = true;
                continue;
            }

            $fset->add($form->get($f->name));
            $form->remove($form->get($f->name));
        }

        $form->add($fsetLeft);
        $form->add($fsetLeftEnd);

        $form->add($fsetRight);
        $form->add($fsetRightEnd);
    }

    public function setupAdminColumnsTabs($fields, $form)
    {
        $dataColumnBreaks = array();

        foreach ($fields as $f) {

            // add data-attributes fo JS
            $notes = $f->notes;

            if (empty($notes)) {
                // try default notes (PW bug with overrides?)
                $notes = $this->fields->get($f->name)->notes;
            }

            if (!empty($notes)) {
                $notes = trim($notes);

                if (strpos($notes, 'colbreak_') !== 0) {
                    return;
                }

                $notes = str_replace('colbreak_', '', $notes);

                if (strpos($notes, ':') !== false) {
                    $notes = array_map('trim', explode(':', $notes));
                } else {
                    $notes = array($notes, 67);
                }
                $dataColumnBreaks[$f->name] = $notes;
            }
        }

        if (!empty($dataColumnBreaks)) {
            $form->wrapAttr('data-column-break', json_encode($dataColumnBreaks));
        }
    }

}
