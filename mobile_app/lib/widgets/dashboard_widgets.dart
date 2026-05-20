import 'package:flutter/material.dart';
import '../theme.dart';

class StatCard extends StatelessWidget {
  final dynamic icon;
  final Color? iconColor;
  final Color? iconBgColor;
  final String value;
  final String label;
  final String? title;
  final Color? color;
  final String? footer;

  const StatCard({
    super.key,
    required this.icon,
    required this.value,
    this.iconColor,
    this.iconBgColor,
    this.label = '',
    this.title,
    this.color,
    this.footer,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: color?.withValues(alpha: 0.1) ?? AppColors.primaryBg,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: icon is IconData
                    ? Icon(icon, color: color ?? AppColors.primary, size: 20)
                    : (icon as dynamic)(),
              ),
              if (footer != null && footer!.contains('%'))
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                  decoration: BoxDecoration(
                    color: AppColors.background,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    footer!,
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      color: footer!.startsWith('+')
                          ? Colors.green[600]
                          : Colors.red[600],
                    ),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            value,
            style: const TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: AppColors.textMain,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            label.isNotEmpty ? label : (title ?? ''),
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }
}

class CategoryItem extends StatelessWidget {
  final String name;
  final IconData icon;
  final VoidCallback? onTap;
  final String? title;

  const CategoryItem({
    super.key,
    this.name = '',
    required this.icon,
    this.onTap,
    this.title,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: AppColors.primaryBg,
              borderRadius: BorderRadius.circular(16),
              border:
                  Border.all(color: AppColors.border.withValues(alpha: 0.5)),
            ),
            child: Icon(icon, color: AppColors.primary, size: 24),
          ),
          const SizedBox(height: 8),
          Text(
            name.isNotEmpty ? name : (title ?? ''),
            style: const TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: AppColors.textMain,
            ),
            textAlign: TextAlign.center,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }
}
