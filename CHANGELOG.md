# Changelog

All notable changes to `livewire-tiptap` will be documented in this file.

## 1.7.0 - 2026-09-29

Add the `shortcodes` attribute: an arbitrary set of `[code]` markers defined by
the application. In the editor a marker is an atomic chip with a toolbar button;
in the saved HTML it stays plain text, so existing content and the code that
parses markers need no changes.

Add the `with-html` attribute: a toolbar button that replaces the editor with
a field holding the HTML as it is stored. Edits reach the model as they are
typed; on the way back the text passes through the editor schema.

Fix the toolbar tooltip jumping sideways at the end of its fade-in.

Without the new attributes the behaviour is unchanged.

## 1.6.0 - 2026-09-29

Support Livewire 4

## 1.5.0 - 2026-09-01

Add tools settings

## 1.4.0 - 2026-09-01

Add the `tools` attribute: an explicit list of allowed formatting.
It filters both the toolbar and the editor extensions, so hidden formatting
cannot be produced by a shortcut or by pasting. Without `tools` the behaviour
is unchanged.

## 1.3.0 - 2026-02-24

Add translations (ru, en)

## 1.2.0 - 2026-01-14

Add tables
