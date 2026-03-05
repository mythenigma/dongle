import 'package:flutter_test/flutter_test.dart';

import 'package:flutter_application_1/main.dart';

void main() {
  testWidgets('Home page shows cloud sharing entry', (WidgetTester tester) async {
    await tester.pumpWidget(const MyApp());

    expect(find.text('MaiPDF'), findsOneWidget);
    expect(find.text('Online MaiPDF Cloud Sharing'), findsOneWidget);
    expect(find.text('START'), findsOneWidget);
  });

  testWidgets('Tapping START opens native maipdf2026 screen', (WidgetTester tester) async {
    await tester.pumpWidget(const MyApp());

    await tester.tap(find.text('START'));
    await tester.pumpAndSettle();

    expect(find.text('1: Upload File'), findsOneWidget);
    expect(find.text('Choose File'), findsOneWidget);
  });
}
