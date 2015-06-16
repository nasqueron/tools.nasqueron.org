/** @jsx React.DOM */

/*  -------------------------------------------------------------
    Generators - Craft sentences, expressions or generate names.
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Dereckson
    Dependencies:   React, JSX, classnames, react-select
    Licence:        BSD
    -------------------------------------------------------------    */

/*  -------------------------------------------------------------
    Dependencies
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -   */

require('babel/polyfill');

var React = require('react');
var Select = require('react-select');
var Textarea = require('react-textarea-autosize');

/*  -------------------------------------------------------------
    Strings and Array prototype functions
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -   */

String.prototype.capitalizeFirstLetter = function() {
	// http://stackoverflow.com/a/1026087/1930997
    return this.charAt(0).toUpperCase() + this.slice(1);
}

Array.prototype.randomElement = function () {
	// http://stackoverflow.com/a/7120353/1930997
    return this[Math.floor(Math.random() * this.length)]
}

/*  -------------------------------------------------------------
    Craft
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -   */

var CraftBluePrint = {

	bluePrint: null,

	init: function (bluePrint) {
		this.bluePrint = bluePrint;
	},

	getSource: function () {
		return this.bluePrint.craft.source;
	},

	craftPickSeveralInOneGroup: function () {
		/**
		 * In this mode, we have one group object array, and we select craft.amount items in this group.
		 * We return these picked items, concatenated, optionally taking craft.separator in consideration.
		 */
		var separator = "", start = "", end = "";
		if (this.bluePrint.craft.separator) {
			separator = this.bluePrint.craft.separator;
		}
		if (this.bluePrint.craft.prepend) {
			start = this.bluePrint.craft.prepend;
		}
		if (this.bluePrint.craft.append) {
			end = this.bluePrint.craft.append;
		}
		var items = [];
		for (var i = 0 ; i < this.bluePrint.craft.amount ; i++) {
			do {
				candidate = this.bluePrint.group.content.randomElement();
			} while (items.indexOf(candidate) > -1)
			items[i] = candidate;
		};
		return start + items.join(separator) + end;
	},

	craftPickOnePerGroup: function () {
		/**
		 * In this mode, we have a groups array, and we select one item in each group.
		 * We return these picked items, concatenated, optionally taking craft.separator in consideration.
		 */
		var separator = "";
		if (this.bluePrint.craft.separator) {
			separator = this.bluePrint.craft.separator;
		}
		var items = this.bluePrint.groups.map(function (group) {
			return group.content.randomElement();
		});
		return items.join(separator);
	},

	craft: function () {
		/* The blueprint should define a craft mode and other options:
		   craft: { mode: "Quux", ... };

		   We then call the mode method, for example here craftQuux().
		*/

		var craftModeMethod = "craft" + this.bluePrint.craft.mode;
		var itemsAmount = 1;
		var itemsSeparator = "\n";

		if (this.bluePrint.craft.populate) {
			itemsAmount = this.bluePrint.craft.populate;
		}
		if (this.bluePrint.craft.populateSeparator) {
			itemsSeparator = this.bluePrint.craft.populateSeparator;
		}

		if (this[craftModeMethod]) {
			if (itemsAmount == 1) {
				return this[craftModeMethod]();
			}

			var items = [];
			for (i = 0 ; i < itemsAmount ; i++) {
				items[i] = this[craftModeMethod]();
			}
			return items.join(itemsSeparator);
		}
		return "[Craft exception] Craft mode unknown: " + craftModeMethod;
	},
};

/*  -------------------------------------------------------------
    Application UI
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -   */

var App = React.createClass({
	getDefaultProps: function () {
		return {
			searchable: true,
		};
	},

	getInitialState: function () {
		return {
			generator: null,
			generators: [],
			generatorResult: "(running generator...)",
		}
	},

	extractName: function (url) {
			var re = /^.*\/(.*).json$/;
			return re.exec(url)[1];
	},

    componentDidMount: function () {
        var self = this;
        var url = generators.datasource;
        $.getJSON(url, function (result) {
			if (!result || !result.length) {
                return;
            }
			var generatorsList = result.map(function (item) {
				return {
					url: item,
					name: self.extractName(item)
				}
			});
			self.setState({
				generators: generatorsList
			});
        });
    },

	updateGeneratorValue: function (newValue) {
		var url = newValue || null;
		this.setState({
			generator: url
		});
		if (url) {
			this.runGenerator(url);
		}
	},

    getOptions: function () {
        return this.state.generators.map(function (item) {
			return { value: item.url, label: item.name };
		});
    },

	renderSelector: function () {
		return (
            <div id="generator-selector">
				<h2>Generator selection</h2>
				<label htmlFor="generator">Generator to use:</label>
    	        <Select
					name="generator" id="generator"
					options={this.getOptions()}
					value={this.state.generator}
					searchable={this.props.searchable}
					onChange={this.updateGeneratorValue}
				/>
				<span>Something else to craft? We'll be happy to add open datasources and provide the generator logic. <a href="https://devcentral.nasqueron.org/tag/tools/">Suggest it here.</a></span>
			</div>);
	},

	canRenderGenerator: function () {
		return this.state.generator != null;
	},

	getGeneratorTitle: function () {
		return this.extractName(this.state.generator).replace("-", " — ").capitalizeFirstLetter();
	},

	recraft: function () {
		this.setState({
			generatorResult: CraftBluePrint.craft()
		});
	},

	runGenerator: function (url) {
		var self = this;
	    $.getJSON(url, function (result) {
			if (!result) {
                return;
            }
			CraftBluePrint.init(result);
			self.setState({
				generatorResult: CraftBluePrint.craft()
			});
		});
	},

	renderGenerator: function () {
		if (!this.canRenderGenerator()) {
			return "";
		}

		return <div id="generator-result">
			<h2>{this.getGeneratorTitle()}</h2>
			<Textarea id="generator-result" className="result" value={this.state.generatorResult} readOnly />
			<button className="button" onClick={this.recraft}>Roll again</button>
			<p id="generator-source"><strong>Source:</strong> {CraftBluePrint.getSource()}</p>
		</div>;
	},

    render: function () {
		return <div id="app">
			{this.renderSelector()}
			{this.renderGenerator()}
		</div>;
    }
});

/*  -------------------------------------------------------------
    Application entry point
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -   */

var generators = {
	datasource: null,
    id: null,

	init: function (datasource, id) {
		this.datasource = datasource;
		this.id = id;
	},

    getAppElement: function () {
        return document.getElementById(this.id);
    },

	run: function () {
        console.log('Rendering interface...');
        React.render(
            <App/>,
			this.getAppElement()
        );
        console.log('... done.');
	}
}

/*  -------------------------------------------------------------
    Runs application
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -   */

generators.init('list-generators.query', 'application');
generators.run();
