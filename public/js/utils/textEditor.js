import { Editor, Node } from '@tiptap/core';
import {Placeholder} from "@tiptap/extension-placeholder";
import {Document} from "@tiptap/extension-document";
import StarterKit from '@tiptap/starter-kit';
import Highlight from '@tiptap/extension-highlight';
import Underline from '@tiptap/extension-underline';
import Image from '@tiptap/extension-image';
import {Typography} from "@tiptap/extension-typography";
import {TextAlign} from "@tiptap/extension-text-align";
import {Color} from "@tiptap/extension-color";
import {TextStyle} from "@tiptap/extension-text-style";
import {FontFamily} from "@tiptap/extension-font-family";

import { initFlowbite } from "flowbite";
initFlowbite()

//Custom node for <span>
const BackgroundImageSpan = Node.create({
    name: 'backgroundImageSpan',
    group: 'inline',
    inline: true,
    atom: true,

    addAttributes() {
        return {
            src: {default: null },
            text: { default: 'default text' },
        };
    },
    parseHTML() {
        return [{ tag: 'span[data-background-image]' }];
    },
    renderHTML({ HTMLAttributes }) {
        const isVisible = HTMLAttributes.text === ' LEGENDS LIMITED ';

        //font-size: .75rem;
        return [
            'span',
            {
                'data-background-image': '',
                style: `
                display:inline-block;
                background-image:url('${HTMLAttributes.src}');
                background-size: ${isVisible ? '100% 125%' : 'contain'};
                background-repeat: no-repeat;
                background-position: center;
                font-family: sans-serif;
                font-weight: normal;
                color: ${isVisible ? '#424242' : 'transparent'};
                padding: .25em .75em;`,
            },
            HTMLAttributes.text,
        ];
    },
});

const CustomDocument = Document.extend({
    content: 'heading block*',
})

const editor = new Editor({
    element: document.querySelector("#wysiwyg"),
    extensions: [
        CustomDocument,
        StarterKit.configure({
            document: false,
        }),
        Placeholder.configure({
            placeholder: ({ node }) => {
                if (node.type.name === 'heading') {
                    return 'Quel est le titre de l\'article?'
                }
            },
        }),
        Highlight,
        Underline,
        Image.configure({
            HTMLAttributes: {
                class: 'kamenewsArticleImage',
            }
        }),
        BackgroundImageSpan,
        Typography,
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        Color,
        TextStyle,
        FontFamily,
    ],
    content: `
        
      `,
    editorProps: {
        attributes: {
            class: 'format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none h-[24rem]',
        },
    },
    // Listen to state transactions (selection and content updates)
    onTransaction({ editor }) {
        updateMarkButtons();
    }
});

function addImageToEditor(location, filename, text = "default text") {
    const src = `/public/images/${location}/${filename}.ico`;

    // Insert the custom node at the current cursor position
    editor.chain().focus().insertContent({
        type: 'backgroundImageSpan',
        attrs: { src, text }
    }).run();
}


function updateMarkButtons() {
    const marks = ['bold', 'italic', 'underline', 'strike', 'highlight', 'list', 'orderedList', 'alignLeft', 'alignCenter', 'alignRight'];

    marks.forEach((mark) => {
        const button = document.getElementById(`toggle${mark.charAt(0).toUpperCase() + mark.slice(1)}Button`);

        // Check if the mark is active at the current cursor position
        if (editor.isActive(mark)) {
            button.classList.add('bg-gray-200');  // Mark is active, highlight button
        } else {
            button.classList.remove('bg-gray-200');  // Mark is not active, remove highlight
        }
    });
}

function toggleMarkType(markType) {
    const { from, to } = editor.state.selection;

    // Check if there's no selection (cursor only)
    if (from === to) {
        editor.chain().focus().toggleMark(markType).run();
    } else { // If there is a selection
        if (editor.isActive(markType)) {
            editor.chain().focus().unsetMark(markType).run();
        } else {
            editor.chain().focus().setMark(markType).run();
        }
    }

    // Immediately update buttons after the change
    updateMarkButtons();
}

// Event listeners for styling buttons
document.getElementById('toggleBoldButton').addEventListener('click', function() {toggleMarkType('bold');});

document.getElementById('toggleItalicButton').addEventListener('click', function() {toggleMarkType('italic');});

document.getElementById('toggleUnderlineButton').addEventListener('click', function() {toggleMarkType('underline');});

document.getElementById('toggleStrikeButton').addEventListener('click', function() {toggleMarkType('strike');});

document.getElementById('toggleHighlightButton').addEventListener('click', function() {toggleMarkType('highlight');});

document.getElementById('toggleListButton').addEventListener('click', () => editor.chain().focus().toggleBulletList().run());

document.getElementById('toggleOrderedListButton').addEventListener('click', () => editor.chain().focus().toggleOrderedList().run());

document.getElementById('toggleAlignLeftButton').addEventListener('click', () => editor.chain().focus().setTextAlign('left').run());

document.getElementById('toggleAlignCenterButton').addEventListener('click', () => editor.chain().focus().setTextAlign('center').run());

document.getElementById('toggleAlignRightButton').addEventListener('click', () => editor.chain().focus().setTextAlign('right').run());

document.getElementById('toggleBlockquoteButton').addEventListener('click', () => editor.chain().focus().toggleBlockquote().run());

const colorPicker = document.getElementById('RTEcolor');
colorPicker.addEventListener('input', (event) => {
    const selectedColor = event.target.value;

    editor.chain().focus().setColor(selectedColor).run();
})
document.querySelectorAll('[data-hex-color]').forEach((button) => {
    button.addEventListener('click', () => {
        const selectedColor = button.getAttribute('data-hex-color');

        // Apply the selected color to the selected text
        editor.chain().focus().setColor(selectedColor).run();
    });
});
document.getElementById('reset-color').addEventListener('click', () => {
    editor.commands.unsetColor();
})


document.querySelectorAll('[data-font-family]').forEach((button) => {
   button.addEventListener('click', () => {
       const fontFamily = button.getAttribute('data-font-family');
       editor.chain().focus().setFontFamily(fontFamily).run()

       document.getElementById('fontFamilyDropdown').ariaHidden = "true";


   });
});



document.getElementById('toggleParagraphButton').addEventListener('click', () => {
    editor.chain().focus().setParagraph().run();
    document.getElementById('typographyDropdown').ariaHidden = "true";
});
document.querySelectorAll('[data-heading-level]').forEach((button) => {
    button.addEventListener('click', () => {
        const level = button.getAttribute('data-heading-level');
        editor.chain().focus().toggleHeading({ level: parseInt(level) }).run()
        document.getElementById('typographyDropdown').ariaHidden = "true";
    });
});


document.getElementById('addImageButton').addEventListener('click', () => {
    const url = window.prompt('Entrez l\'url imgur de l\'image à ajouter:', 'https://placehold.co/600x400');
    const alt = window.prompt('Entrez le texte alternatif (si l\'image ne s\'affiche pas):', 'Image d\'article')
    if (url && alt) {
        editor.chain().focus().setImage({ src: url , alt: alt}).run();
    }
});

document.getElementById('toggleHardBreakButton').addEventListener('click', () => {
    editor.chain().focus().setHardBreak().run()
})

document.getElementById('toggleHorizontalRuleButton').addEventListener('click', () => {
    editor.chain().focus().setHorizontalRule().run()
})

/* Event listeners for custom GuildMasters buttons*/
//LF
document.getElementById('LFButton').addEventListener('click', function() {
    addImageToEditor('assets', 'lf_plate_empty', ' LEGENDS LIMITED '); // LF
});

//Rarities
document.getElementById('addRarityHE').addEventListener('click', function() {
   addImageToEditor('rarities', 'HERO', 'HE');
});
document.getElementById('addRarityEX').addEventListener('click', function() {
    addImageToEditor('rarities', 'EXTREME', 'EX');
});
document.getElementById('addRaritySP').addEventListener('click', function() {
    addImageToEditor('rarities', 'SPARKING', 'SP');
});
document.getElementById('addRarityUL').addEventListener('click', function() {
    addImageToEditor('rarities', 'ULTRA', 'UL');
});

//Colors
document.getElementById('addColorLUM').addEventListener('click', function() {
    addImageToEditor('colors', 'LUM', 'LU');
});
document.getElementById('addColorROU').addEventListener('click', function() {
    addImageToEditor('colors', 'ROU', 'RO');
});
document.getElementById('addColorJAU').addEventListener('click', function() {
    addImageToEditor('colors', 'JAU', 'JA');
});
document.getElementById('addColorVIO').addEventListener('click', function() {
    addImageToEditor('colors', 'VIO', 'VI');
});
document.getElementById('addColorVER').addEventListener('click', function() {
    addImageToEditor('colors', 'VER', 'VE');
});
document.getElementById('addColorBLE').addEventListener('click', function() {
    addImageToEditor('colors', 'BLE', 'BL');
});

//Cards
document.getElementById('addCardSTR').addEventListener('click', function() {
    addImageToEditor('cards', 'strike', 'ST');
});
document.getElementById('addCardBLA').addEventListener('click', function() {
    addImageToEditor('cards', 'blast', 'BL');
});
document.getElementById('addCardUNI').addEventListener('click', function() {
    addImageToEditor('cards', 'unique', 'UN');
});
document.getElementById('addCardSPE').addEventListener('click', function() {
    addImageToEditor('cards', 'special', 'SP');
});
document.getElementById('addCardULT').addEventListener('click', function() {
    addImageToEditor('cards', 'ultimate', 'UL');
});
document.getElementById('addCardAWA').addEventListener('click', function() {
    addImageToEditor('cards', 'awakened', 'AW');
});

//Gauges
document.getElementById('addGaugeATT').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_attack', 'AT');
});
document.getElementById('addGaugeCHA').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_charge', 'CH');
});
document.getElementById('addGaugeCOU').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_counter', 'CO');
});
document.getElementById('addGaugeEVA').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_evade', 'EV');
});
document.getElementById('addGaugeSUS').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_sustained', 'SU');
});
document.getElementById('addGaugeSWI').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_switch', 'SW');
});
document.getElementById('addGaugeTIM').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_time', 'TI');
});
document.getElementById('addGaugeDRA').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_dragonball', 'DR');
});
document.getElementById('addGaugeSYN').addEventListener('click', function() {
    addImageToEditor('gauges', 'g_synchro', 'SY');
});

document.getElementById('toggleHTMLButton').addEventListener('click', async () => {

    // basically just use editor.getHTML(); to get the raw html

    let articleHTML = editor.getHTML()
        .replace(/&/g, "&amp;") // Escape & character
        .replace(/</g, "&lt;")  // Escape < character
        .replace(/>/g, "&gt;")  // Escape > character
        .replace(/"/g, "&quot;") // Escape " character
        .replace(/'/g, "&#039;"); // Escape ' character

    document.getElementById("content").innerHTML = articleHTML;
    document.getElementById('title').value = document.getElementById('title-input').value;
});