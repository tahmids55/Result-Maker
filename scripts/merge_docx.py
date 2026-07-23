import sys
from docxcompose.composer import Composer
from docx import Document
from docx.opc.phys_pkg import _ZipPkgReader

# 1x1 transparent PNG
DUMMY_PNG = b'\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82'

# Monkey-patch python-docx to ignore missing files in corrupted docx archives
original_blob_for = _ZipPkgReader.blob_for
def safe_blob_for(self, pack_uri):
    try:
        return original_blob_for(self, pack_uri)
    except KeyError as e:
        name = pack_uri.lower()
        if name.endswith(('.png', '.jpg', '.jpeg', '.gif', '.svg', '.bmp', '.tif', '.tiff')):
            return DUMMY_PNG
        raise
_ZipPkgReader.blob_for = safe_blob_for

def combine_all_docx(filename_master, files_list, output_filename):
    if not files_list:
        return

    # Open the first document as the master
    master = Document(filename_master)
    composer = Composer(master)
    
    # Append the rest
    for file in files_list:
        doc = Document(file)
        # Add a page break if needed? docxcompose usually handles append without explicitly needing a page break,
        # but to ensure each student's marksheet starts on a new page, we might want to add a page break.
        # However, typically marksheet templates themselves occupy a full page.
        # Let's add a page break to the master before appending the next doc
        master.add_page_break()
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
