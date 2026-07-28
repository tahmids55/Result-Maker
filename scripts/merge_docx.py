import sys
from docxcompose.composer import Composer
from docx import Document
from docx.opc.phys_pkg import _ZipPkgReader
from docx.oxml.ns import qn
from docx.oxml import OxmlElement

# 1x1 transparent PNG
DUMMY_PNG = b'\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82'

# Monkey-patch python-docx to ignore missing files in corrupted docx archives
original_blob_for = _ZipPkgReader.blob_for
def safe_blob_for(self, pack_uri):
    try:
        return original_blob_for(self, pack_uri)
    except KeyError as e:
        name = str(pack_uri).lower()
        if name.endswith(('.png', '.jpg', '.jpeg', '.gif', '.svg', '.bmp', '.tif', '.tiff')):
            return DUMMY_PNG
        raise
_ZipPkgReader.blob_for = safe_blob_for

def _set_page_break_before(element):
    """Finds the first paragraph (w:p) in this element and sets pageBreakBefore=True."""
    for child in element:
        if child.tag == qn('w:p'):
            pPr = child.find(qn('w:pPr'))
            if pPr is None:
                pPr = OxmlElement('w:pPr')
                child.insert(0, pPr)
            pb = pPr.find(qn('w:pageBreakBefore'))
            if pb is None:
                pb = OxmlElement('w:pageBreakBefore')
                pPr.append(pb)
            return True
        elif child.tag == qn('w:tbl'):
            # If the first element is a table, we need to set pageBreakBefore on the first paragraph inside it
            if _set_page_break_before(child):
                return True
    return False

def _remove_trailing_empty_paras(body):
    """
    Removes trailing empty paragraphs from a document body.
    Word requires documents to end with a paragraph, so if a document ends with a table,
    Word adds an empty paragraph. If this empty paragraph spills to a new page, and we
    append a new marksheet with a page break, that empty paragraph creates a blank page!
    Removing it before appending the next marksheet solves the issue.
    """
    children = list(body)
    for child in reversed(children):
        if child.tag == qn('w:sectPr'):
            continue
        if child.tag == qn('w:p'):
            # Check if paragraph is empty (no text, no images)
            text = ''.join(t.text for t in child.findall('.//' + qn('w:t')) if t.text)
            drawings = child.findall('.//' + qn('w:drawing'))
            picts = child.findall('.//' + qn('w:pict'))
            
            if not text.strip() and not drawings and not picts:
                body.remove(child)
            else:
                break # Stop at first paragraph that has content
        else:
            break # Stop if we hit a table or other element

def combine_all_docx(filename_master, files_list, output_filename):
    if not files_list:
        return

    # Open the first document as the master
    master = Document(filename_master)
    composer = Composer(master)
    
    # Append the rest
    for file in files_list:
        # Before appending, remove the trailing empty paragraph from the master
        # to prevent it from spilling over and causing a blank page.
        _remove_trailing_empty_paras(master.element.body)
        
        doc = Document(file)
        
        # Set 'pageBreakBefore' on the very first paragraph of the appended document.
        # This tells MS Word to start this paragraph at the top of a new page.
        _set_page_break_before(doc.element.body)
        
        composer.append(doc)
    
    composer.save(output_filename)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python merge_docx.py <output.docx> <input1.docx> <input2.docx> ...")
        sys.exit(1)
    
    output_file = sys.argv[1]
    inputs = sys.argv[2:]
    
    # Master is the first input
    combine_all_docx(inputs[0], inputs[1:], output_file)
    print("Merged successfully to " + output_file)
