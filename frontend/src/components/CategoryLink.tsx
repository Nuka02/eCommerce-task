import React from 'react';
import { NavLink, useResolvedPath, useMatch } from 'react-router-dom';
import { Category } from '../types';

interface CategoryLinkProps {
  category: Category;
}

const CategoryLink: React.FC<CategoryLinkProps> = ({ category }) => {
  const to = `/${category.name}`;
  const resolved = useResolvedPath(to);
  const match = useMatch({ path: resolved.pathname, end: true });

  return (
    <NavLink
      to={to}
      className={match ? 'header__link header__link--active' : 'header__link'}
      data-testid={match ? 'active-category-link' : 'category-link'}
    >
      {category.name.toUpperCase()}
    </NavLink>
  );
};

export default CategoryLink;
