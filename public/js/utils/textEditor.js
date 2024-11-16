import { Editor, Node } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit'; /*remove 'https://esm.sh/' to convert from CDN to vanilla JS*/
import Highlight from '@tiptap/extension-highlight'; /*remove 'https://esm.sh/' to convert from CDN to vanilla JS*/
import Underline from '@tiptap/extension-underline'; /*remove 'https://esm.sh/' to convert from CDN to vanilla JS*/
import Image from '@tiptap/extension-image'; /*remove 'https://esm.sh/' to convert from CDN to vanilla JS*/


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

        return [
            'span',
            {
                'data-background-image': '',
                style: `
                display:inline-block;
                font-size: .75rem;
                background-image:url('${HTMLAttributes.src}');
                background-size:contain;
                background-repeat: no-repeat;
                font-family: sans-serif;
                color: ${isVisible ? '#424242' : 'transparent'};
                padding: .25em .75em;`,
            },
            HTMLAttributes.text,
        ];
    },
});


const editor = new Editor({
    element: document.querySelector("#wysiwyg"),
    extensions: [
        StarterKit,
        Highlight,
        Underline,
        Image,
        BackgroundImageSpan,
    ],
    content: `
        
      `,
    editorProps: {
        attributes: {
            class: 'format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none h-[24rem] list-disc',
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
    const marks = ['bold', 'italic', 'underline', 'strike', 'highlight', 'list', 'orderedList'];

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