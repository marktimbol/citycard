import React from 'react';
import ReactDOM from 'react-dom';
import Card from '../Card.js';
import Event from '../Event.js';
import OutletCard from '../Outlet/OutletCard.js';

class SearchResults extends React.Component
{
	render()
	{
		let outlet_results = app.data.outlets.map(outlet => {
			return (
				<OutletCard outlet={outlet} key={outlet.id} />
			)
		})

		let post_results = app.data.posts.map(post => {
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
				<h4>Outlets</h4>			
				<div className="Explore--container no-margin-top">
					{ outlet_results }
				</div>
				{ post_results }
			</div>
		)
	}
}

ReactDOM.render(
	<SearchResults />,
	document.getElementById('SearchResults')
);