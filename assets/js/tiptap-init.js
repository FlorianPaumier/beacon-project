import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Link from "@tiptap/extension-link";
import Placeholder from "@tiptap/extension-placeholder";
import Image from "@tiptap/extension-image";

const actionMap = {
    toggleBold: (e) => e.chain().focus().toggleBold().run(),
    toggleItalic: (e) => e.chain().focus().toggleItalic().run(),
    toggleStrike: (e) => e.chain().focus().toggleStrike().run(),
    toggleHeading: (e, args) => e.chain().focus().toggleHeading(args).run(),
    toggleBulletList: (e) => e.chain().focus().toggleBulletList().run(),
    toggleOrderedList: (e) => e.chain().focus().toggleOrderedList().run(),
    toggleBlockquote: (e) => e.chain().focus().toggleBlockquote().run(),
    toggleCodeBlock: (e) => e.chain().focus().toggleCodeBlock().run(),
    setLink: (e) => {
        const prev = e.getAttributes("link").href;
        const url = window.prompt("URL", prev || "https://");
        if (url === null) return;
        if (url === "") {
            e.chain().focus().unsetLink().run();
        } else {
            e.chain().focus().setLink({ href: url }).run();
        }
    },
    undo: (e) => e.chain().focus().undo().run(),
    redo: (e) => e.chain().focus().redo().run(),
};

function uploadFile(file, editor) {
    if (!file.type.startsWith("image/")) return;

    const formData = new FormData();
    formData.append("file", file);

    fetch("/admin/upload", {
        method: "POST",
        body: formData,
    })
        .then((res) => {
            if (!res.ok) throw new Error("Upload failed");
            return res.json();
        })
        .then((data) => {
            editor.chain().focus().setImage({ src: data.url }).run();
        })
        .catch(() => {
            // silently fail
        });
}

document.querySelectorAll("[data-beacon-tiptap]").forEach((element) => {
    const input = document.getElementById(element.dataset.input);
    if (!input) return;

    const maxLength = parseInt(element.dataset.maxLength || "0", 10);
    const placeholder = element.dataset.placeholder || "";
    const toolbar = element.parentElement.querySelector(".fi-fo-rich-editor-toolbar");

    if (placeholder) {
        element.dataset.placeholder = placeholder;
    }

    const editor = new Editor({
        element,
        extensions: [
            StarterKit.configure({
                heading: { levels: [2, 3] },
            }),
            Link.configure({
                openOnClick: true,
                HTMLAttributes: { rel: "noopener noreferrer", target: "_blank" },
            }),
            Placeholder.configure({ placeholder }),
            Image.configure({
                inline: false,
                allowBase64: false,
            }),
        ],
        content: input.value || "",
        editorProps: {
            handleDrop: (view, event) => {
                const files = event.dataTransfer?.files;
                if (files?.length) {
                    event.preventDefault();
                    Array.from(files).forEach((f) => uploadFile(f, editor));
                    return true;
                }
                return false;
            },
            handlePaste: (view, event) => {
                const items = event.clipboardData?.items;
                if (items?.length) {
                    for (const item of items) {
                        if (item.type.startsWith("image/")) {
                            event.preventDefault();
                            const file = item.getAsFile();
                            if (file) uploadFile(file, editor);
                            return true;
                        }
                    }
                }
                return false;
            },
        },
        onUpdate: ({ editor }) => {
            const html = editor.getHTML();
            if (maxLength > 0 && html.length > maxLength) return;
            input.value = html;
            input.dispatchEvent(new Event("input", { bubbles: true }));
        },
    });

    element._beaconEditor = editor;

    if (toolbar) {
        toolbar.querySelectorAll("button[data-beacon-tiptap-action]").forEach((btn) => {
            btn.addEventListener("mousedown", (e) => {
                e.preventDefault();
                const action = btn.dataset.beaconTiptapAction;
                const args = btn.dataset.beaconTiptapArgs
                    ? JSON.parse(btn.dataset.beaconTiptapArgs)
                    : {};
                const fn = actionMap[action];
                if (fn) fn(editor, args);
                editor.chain().focus().run();
            });
        });

        const imageBtn = toolbar.querySelector("[data-beacon-tiptap-action='uploadImage']");
        if (imageBtn) {
            const fileInput = document.createElement("input");
            fileInput.type = "file";
            fileInput.accept = "image/*";
            fileInput.style.display = "none";
            fileInput.addEventListener("change", () => {
                const file = fileInput.files?.[0];
                if (file) uploadFile(file, editor);
                fileInput.value = "";
            });
            imageBtn.parentNode.appendChild(fileInput);
            imageBtn.addEventListener("mousedown", (e) => {
                e.preventDefault();
                fileInput.click();
            });
        }

        editor.on("selectionUpdate", () => updateToolbar(editor, toolbar));
        updateToolbar(editor, toolbar);
    }
});

function updateToolbar(editor, toolbar) {
    if (!toolbar) return;
    toolbar.querySelectorAll("button[data-beacon-tiptap-action]").forEach((btn) => {
        const action = btn.dataset.beaconTiptapAction;
        const args = btn.dataset.beaconTiptapArgs
            ? JSON.parse(btn.dataset.beaconTiptapArgs)
            : {};
        let isActive = false;

        switch (action) {
            case "toggleBold": isActive = editor.isActive("bold"); break;
            case "toggleItalic": isActive = editor.isActive("italic"); break;
            case "toggleStrike": isActive = editor.isActive("strike"); break;
            case "toggleHeading": isActive = editor.isActive("heading", args); break;
            case "toggleBulletList": isActive = editor.isActive("bulletList"); break;
            case "toggleOrderedList": isActive = editor.isActive("orderedList"); break;
            case "toggleBlockquote": isActive = editor.isActive("blockquote"); break;
            case "toggleCodeBlock": isActive = editor.isActive("codeBlock"); break;
            case "setLink": isActive = editor.isActive("link"); break;
        }

        btn.classList.toggle("is-active", isActive);
    });
}
