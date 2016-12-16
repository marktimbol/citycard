import React from 'react';
import ReactDOM from 'react-dom';
import InfiniteScroll from 'react-infinite-scroller';
import Card from './Card';

class PostsInfinite extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			posts: app.posts,
			nextPageUrl: app.nextPageUrl,
			hasMorePages: app.hasMorePages,
		}

		this.getNewPosts = this.getNewPosts.bind(this);
	}

    getNewPosts() {
    	let that = this;

        $.getJSON(that.state.nextPageUrl, function(response) {
	    	that.setState({
	    		hasMorePages: response.hasMorePages,
	    		nextPageUrl: response.nextPageUrl,
	    		posts: that.state.posts.concat(response.posts)
	    	})

	    	console.log(response);
        });
    }

	render()
	{
		let posts = this.state.posts.map((post, index) => {
			return (
				<Card item={post} key={index} />
			)
		})

		return (
	        <InfiniteScroll
	        	pageStart={0}
	            loadMore={this.getNewPosts}
	            hasMore={this.state.hasMorePages}
	            loader={<div className="loader">Loading ...</div>}
			>
	            {posts}
	        </InfiniteScroll>			
		)
	}
}

ReactDOM.render(
	<PostsInfinite />,
	document.getElementById('PostsInfinite')
);