/**
 * PostGrid Social Icons Integration Example
 * 
 * This shows how to integrate the lightweight social icons
 * into the existing PostGrid block
 */

// Import the social icons
import { SocialIcon, SocialIconsGroup } from './social-icons/social-icons';

// Example of adding social sharing to each post item
export const PostGridItemWithSharing = ({ post, showDate, showExcerpt }) => {
	// Generate sharing URLs
	const shareUrls = {
		facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(post.link)}`,
		twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(post.link)}&text=${encodeURIComponent(post.title)}`,
		linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(post.link)}`,
		pinterest: `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(post.link)}&description=${encodeURIComponent(post.title)}`,
		email: `mailto:?subject=${encodeURIComponent(post.title)}&body=${encodeURIComponent(post.link)}`,
	};

	return (
		<article className="wp-block-postgrid__item">
			<h3 className="wp-block-postgrid__title">
				<a href={post.link}>{post.title}</a>
			</h3>
			
			{showDate && (
				<time className="wp-block-postgrid__date">
					{post.date}
				</time>
			)}
			
			{showExcerpt && (
				<div 
					className="wp-block-postgrid__excerpt"
					dangerouslySetInnerHTML={{ __html: post.excerpt }}
				/>
			)}
			
			{/* Social sharing icons */}
			<div className="postgrid-social-icons">
				<SocialIcon icon="facebook" url={shareUrls.facebook} size="small" />
				<SocialIcon icon="twitter" url={shareUrls.twitter} size="small" />
				<SocialIcon icon="linkedin" url={shareUrls.linkedin} size="small" />
				<SocialIcon icon="pinterest" url={shareUrls.pinterest} size="small" />
				<SocialIcon icon="email" url={shareUrls.email} size="small" />
			</div>
		</article>
	);
};

// Or use the group component with configuration
export const PostGridItemWithSharingGroup = ({ post, showDate, showExcerpt }) => {
	const socialLinks = [
		{ icon: 'facebook', url: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(post.link)}` },
		{ icon: 'twitter', url: `https://twitter.com/intent/tweet?url=${encodeURIComponent(post.link)}&text=${encodeURIComponent(post.title)}` },
		{ icon: 'linkedin', url: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(post.link)}` },
		{ icon: 'pinterest', url: `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(post.link)}&description=${encodeURIComponent(post.title)}` },
		{ icon: 'email', url: `mailto:?subject=${encodeURIComponent(post.title)}&body=${encodeURIComponent(post.link)}` },
	];

	return (
		<article className="wp-block-postgrid__item">
			<h3 className="wp-block-postgrid__title">
				<a href={post.link}>{post.title}</a>
			</h3>
			
			{showDate && (
				<time className="wp-block-postgrid__date">
					{post.date}
				</time>
			)}
			
			{showExcerpt && (
				<div 
					className="wp-block-postgrid__excerpt"
					dangerouslySetInnerHTML={{ __html: post.excerpt }}
				/>
			)}
			
			<SocialIconsGroup icons={socialLinks} size="small" />
		</article>
	);
};
