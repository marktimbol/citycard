import React from 'react';
import ReactDOM from 'react-dom';
import Card from '../Card.js';
import Event from '../Event.js';

class SearchResults extends React.Component
{
	render()
	{
		let results = app.data.results.map(post => {
			if( post.type == 'events' )
			{
				return (
					<Event item={post} key={post.id} />
				)
			}

			return (
				<Card item={post} key={post.id} />
			)
		});

		return (
			<div>
				<h3>Search Results</h3>
				{ results }
			</div>
		)
	}
}

ReactDOM.render(
	<SearchResults />,
	document.getElementById('SearchResults')
);