
# Convert directory with yamls to basic html
# Also creates a json file with info search

import os, codecs
import yaml, json

root = os.path.join(os.getcwd())
folder_solitaires = os.path.join(root, "src", "solitaires")
folder_test_folder = os.path.join(root,"html")
json_name = "content.json"
json_path = os.path.join(root, "search_json") # where to save
use_tipue = False 

def scan_folder(path):
	theFiles = list()

	for root, subFolders, files in os.walk(path):
		for filename in files:
			filePath = os.path.join(root, filename)

			if os.path.exists(filePath):
				theFiles.append(filePath)

	return theFiles

def basic_html(yaml_array):
	html = '''<!DOCTYPE html><html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>QSol</title>
	<link rel="stylesheet" type="text/css" href="css/tipuesearch.css">
	</head><body>'''
	html += "<h1>" + yaml_array['name'] + "</h1>"
	html += "<p>" + yaml_array['object'] + "</p>"
	html += '''
	<div id="search">
	<form action="search.html">
	<div style="float: left;"><input type="text" name="q" id="tipue_search_input"></div>
	<div style="float: left; margin-left: 13px;"><input type="button" id="tipue_search_button" onclick="this.form.submit();"></div>
	<div style="clear: left;"></div>
	</form></div>
	</body></html>'''

	return html 

def json_search(json_pieces, tipue=False):
	""" Finishes the json search file and saves it.

	json_pieces: list of dictionaries.
    """

	if tipue:
		complete = json.dumps({"pages":json_pieces}, indent=2)
	else:
		complete = json.dumps(json_pieces, indent=2)

	save_file(os.path.join(json_path, json_name), complete)


def json_individual(yaml_data, tipue=False):
	""" extract info needed and converts to dictionary"""

	# TODO: how handle multilanguage
	tmp_dict = dict()

	if tipue:
		beautifyTags = list()
		beautifyTags.append("decks/mazos: " + str(yaml_data['caracs']['decks']))
		beautifyTags.append("difficulty/dificultad: " + yaml_data['caracs']['difficulty'])
		if yaml_data['caracs']['type']:
			beautifyTags.append("type: " + yaml_data['caracs']['type'])

		tmp_dict['text'] = ",".join(beautifyTags)
		tmp_dict['tags'] = ""
		if yaml_data['similars'] and yaml_data['similars'][0] is not None:
			tmp_dict['tags'] += ",".join(yaml_data['similars'])

		tmp_dict['loc'] = ""
		tmp_dict['title'] = yaml_data['name']

		if 'aka' in yaml_data and yaml_data['aka']:
			tmp_dict['title'] += " - " + yaml_data['aka']

	else:
		currentName = yaml_data['name']
		tmp_dict[currentName] = dict()
		tmp_dict[currentName]['aka'] = yaml_data['aka']
		tmp_dict[currentName]['decks'] = yaml_data['caracs']['decks']
		tmp_dict[currentName]['difficulty'] = yaml_data['caracs']['difficulty']
		tmp_dict[currentName]['type'] = yaml_data['caracs']['type']
		if yaml_data['similars'] and yaml_data['similars'][0] is not None:
			tmp_dict[currentName]['similars'] = ",".join(yaml_data['similars'])


	return tmp_dict



def save_file(filename, content):
	with codecs.open(filename , 'w', encoding='utf-8') as output:
		output.write(content)




allfiles = scan_folder(folder_solitaires)
if use_tipue:
	allJsons = list()
else:
	allJsons = dict()

for hi in allfiles:
	print (" processing {}".format(hi))
	with open (hi, 'r') as yaml_file:
		data = yaml.load(yaml_file)

	complete = basic_html(data)

	if use_tipue:
		allJsons.append(json_individual(data, use_tipue))
	else:
		allJsons.update(json_individual(data, use_tipue))

	filename, _ = os.path.splitext(os.path.basename(hi))
	filename = filename + ".html"

	save_file(os.path.join(folder_test_folder, filename), complete)



# finish json search
json_search(allJsons, use_tipue)


