<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;
use ProcessWire\Inputfield;
use ProcessWire\RepeaterPage;

class BypassTrash extends Tweak
{
    private $editedPage;
    private $strings;


    public function info(): array
    {
        return [
            'description' => "Add buttons/options, to Page list actions and page edit tab, to bypass trash for SuperUsers",
        ];
    }


    public function ready(): void
    {
        if (!$this->wire->user->isSuperuser()) {
            return;
        }

        // Translatable strings
        $this->strings = new \StdClass();
        $this->strings->cancel = $this->_("Cancel Deletion");
        $this->strings->confirm = $this->_("Delete Permanently");
        $this->strings->skip_trash = $this->_('Skip Trash?');
        $this->strings->desc = $this->_('Check to permanently delete this page.');
        $this->strings->deleted = $this->_('Deleted page: %s');

        $this->addHookAfter('ProcessPageListActions::getExtraActions', $this, 'addDeleteButton');
        $this->addHookAfter('ProcessPageListActions::processAction', $this, 'addDeleteButtonAction');

        // add delete field to page edit Delete tab
        $this->addHookAfter('ProcessPageEdit::buildFormDelete', $this, 'addDeletePermanentlyField');
        $this->addHookBefore('Pages::trash', $this, 'addDeletePermanentlyHook');

        $this->wire->addHookBefore("ProcessPageList::execute", function() {
            $str_cancel  = $this->strings->cancel;
            $str_confirm = $this->strings->confirm;
            $this->wire('config')->js('AOS_BypassTrash', compact('str_cancel', 'str_confirm'));
            $this->loadCSS();
            $this->loadJS();
        });

        $this->editedPage = false;
        $editedPageId = $this->wire('sanitizer')->int($this->wire('input')->get->id);
        $editedPage = $this->wire('pages')->get($editedPageId);

        if ($editedPage->id && !($editedPage instanceof RepeaterPage)) {
            $this->editedPage = $editedPage;
        }
    }


    /**
     * Add Delete button to pagelist
     */
    public function addDeleteButton(HookEvent $event)
    {
        $page = $event->arguments('page');

        if (!$this->wire('user')->isSuperuser()) {
            return false;
        }

        // do not allow for pages having children
        if ($page->numChildren > 0) {
            return false;
        }

        //  not trashable and not in Trash
        if (!$page->trashable() && !$page->isTrash()) {
            return false;
        }

        $actions = array();
        $adminUrl = $this->wire('config')->urls->admin . 'page/';
        $icon = '';
        $actions['delete'] = array(
            'cn' => 'Delete aos-pagelist-confirm',
            'name' => $icon . 'Delete',
            'url' => $adminUrl . '?action=delete&id=' . $page->id,
            'ajax' => true,
        );

        $event->return += $actions;
    }


    /**
     * Process action for addDeleteButton.
     *
     * @return bool
     */
    public function addDeleteButtonAction(HookEvent $event)
    {
        $page = $event->arguments(0);
        $action = $event->arguments(1);
        // do not allow for pages having children
        if ($page->numChildren > 0) {
            return false;
        }

        if ($action == 'delete') {
            $page->delete();
            $event->return = array(
                'action' => $action,
                'success' => true,
                'page' => $page->id,
                'updateItem' => $page->id,
                'message' => 'Page deleted.',
                'remove' => true,
                'refreshChildren' => false,
            );
        }
    }


    public function addDeletePermanentlyField(HookEvent $event)
    {
        if ($this->editedPage && !$this->editedPage->trashable()) {
            return false;
        }

        $form = $event->return;

        $trashConfirmField = $form->get('delete_page');
        if (!$trashConfirmField) {
            return false;
        }

        $f = $this->wire('modules')->get('InputfieldCheckbox');
        $f->attr('id+name', 'delete_permanently');
        $f->checkboxLabel = $this->strings->confirm;
        $f->label = $this->strings->skip_trash;
        $f->description = $this->strings->desc;
        $f->value = '1';

        $trashConfirmField->columnWidth = 50;
        $f->columnWidth = 50;

        $f->collapsed = Inputfield::collapsedNever;
        $trashConfirmField->collapsed = Inputfield::collapsedNever;

        // add fieldset (Reno top spacing bug)
        if ($this->adminTheme === 'AdminThemeReno') {
            $fset = $this->wire('modules')->get('InputfieldFieldset');
            $fset->add($trashConfirmField);
            $fset->add($f);
            $form->remove($trashConfirmField);
            $form->insertBefore($fset, $form->get('submit_delete'));
        } else {
            $form->insertAfter($f, $trashConfirmField);
        }
    }


    // delete page instead trashing if delete_permanently was checked
    public function addDeletePermanentlyHook(HookEvent $event)
    {
        if (isset($this->wire('input')->post->delete_permanently)) {
            $p = $event->arguments[0];
            $session = $this->wire('session');
            $afterDeleteRedirect = $this->wire('config')->urls->admin . "page/?open={$p->parent->id}";
            if ($p->deleteable()) {
                $session->message(sprintf($this->strings->deleted, $p->url)); // Page deleted message
                $this->wire('pages')->delete($p, true);
                $session->redirect($afterDeleteRedirect);
            }
        }
    }
}
