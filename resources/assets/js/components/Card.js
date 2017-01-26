import React from 'react';
import TimeAgo from 'react-timeago';
import moment from 'moment';

class Card extends React.Component
{
	itemDescription() {
		let item = this.props.item;

		return {
			__html: item.desc.substr(0, 300)
		}
	}

	render()
	{
		let item = this.props.item;
		let logo = 'http://placehold.it/28x28';
		let featuredImage = 'http://placehold.it/600x500';

		if( item.merchant.logo != null )
		{
			logo = app.s3_bucket_url + item.merchant.logo;
		}

		if (item.photos.length > 0 ) {
			featuredImage = app.s3_bucket_url + item.photos[0].url;
		}

		let post_url = '/posts/' + item.slug;
		let small_image = 'https://cdn-images-1.medium.com/freeze/max/30/1*XtTMZ5cZ2KWWVFATIr3dpQ.png?q=20';

		return (
			<div className="Card">
				<div className="Card__header">
					<div className="Flex Flex--center">
						<img src={logo} alt={item.merchant.name} title={item.merchant.name} className="img-circle Card__logo" width="30" height="30" />
						<h4 className="Card__title">
							<a href="#">{item.merchant.name}</a>
						</h4>
					</div>
					<div>
						<span className="timeago">
							<TimeAgo date={moment(item.created_at).format('LLL')} />
						</span>
					</div>
				</div>
				<div className="Card__image">
					<a href={post_url}>
						<div className="placeholder" data-large={featuredImage}>
							<img src={small_image} className="img-small" /> 
							<div className="aspect-ratio-fill"></div> 
						</div>					
					</a>
				</div>
				<div className="Card__description">
					<h3><a href={post_url}>{item.title}</a></h3>
					<div dangerouslySetInnerHTML={this.itemDescription()}></div>
				</div>
			</div>
		)
	}
}

export default Card;
