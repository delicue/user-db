<main class="px-4 py-8">
    <h2 class="text-3xl text-gray-800 text-center font-bold mb-8">
        User Database- Manage Your Users Easily
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 container mx-auto h-1/2">
        <!-- User Search -->
        <div class="container mx-auto px-8 py-4 bg-white rounded shadow shadow-sky-900 hover:shadow-lg mb-16 hover:shadow-sky-700">
            <h3 class="text-2xl font-bold mb-4">Search Users</h3>
            <div class="container place-items-center">
                <input id="userSearch" type="text" placeholder="Search by name or email" class="px-2 py-1 rounded w-full bg-gray-50 border border-gray-200 text-gray-800 mb-4">
            </div>
        </div>
        <!-- Add User Form -->
        <form class="container mx-auto px-8 py-4 bg-white rounded shadow shadow-sky-900 hover:shadow-lg mb-16 hover:shadow-sky-700" method="POST" action="/add-user">
            <input type="hidden" name="_csrf_token_add_user" value="<?= htmlspecialchars(generateCsrfToken('add_user')) ?>">
            <h2 class="text-2xl font-bold mb-4">Add New User</h2>

            <div class="gap-4 grid grid-cols-2">
                <div class="flex flex-wrap">
                    <input type="text" autocomplete="name" name="name" placeholder="Enter user name" class="px-2 py-1 rounded w-full bg-gray-50 border border-gray-200 text-gray-800 mb-4">
                    <span class="text-red-600"><?= htmlspecialchars(\App\Forms\AddUserForm::getErrors()['name'] ?? '') ?></span>
                    <input type="email" name="email" autocomplete="email" placeholder="Enter user email" class="px-2 py-1 rounded w-full bg-gray-50 border border-gray-200 text-gray-800 mb-4">
                    <p><span class="text-red-600"><?= htmlspecialchars(\App\Forms\AddUserForm::getErrors()['email'] ?? '') ?></span></p>
                </div>
                <div>
                    <div class="group">
                        <?php $userCount = \App\Database::getInstance()->count('users') ?>
                        <?php $userLimit = \App\Forms\AddUserForm::$rules['user_limit']['objectLimit'] ?>
                        <?php $limitReached = (bool)($userCount >= $userLimit) ?>
                        <input
                            type="submit"
                            class="bg-cyan-600 hover:bg-cyan-800 text-white font-bold disabled:[] not-disabled:cursor-grab disabled:cursor-not-allowed py-1 px-4 rounded"
                            value="Add User">
                        <?php if ($limitReached) : ?>
                            <p class="invisible group-hover:visible text-wrap group-hover:transition:duration-300 group-hover:transform absolute bg-red-400 text-white text-sm rounded py-1 px-2 mt-1">
                                User limit reached. Cannot add more users.</p>
                        <?php endif; ?>
                    </div>
                    <p><span class="text-red-600"><?= htmlspecialchars(\App\Forms\AddUserForm::getErrors()['user_limit'] ?? '') ?></span></p>
                </div>
            </div>
        </form>
    </div>

    <div class="relative h-1/2">
        <!-- User List -->
        <?php if (empty(data('users'))) : ?>
            <p class="text-center text-gray-600 mb-16">No users found. Please add some users.</p>
        <?php else : ?>
            <ul id="userList" class="mb-16 container mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach (data('users') as $user): ?>
                    <!-- List Item Template. Will be populated dynamically via JS fetch -->
                    <li class="user-item bg-white transition-all duration-200 hover:translate-y-1 shadow-lg hover:shadow-2xl rounded-lg">
                        <p class="text-gray-200 bg-slate-700 rounded-t-md p-2 border-b border-b-emerald-200">ID: <?= htmlspecialchars($user['id']) ?></p>
                        <div class="text-slate-700 bg-linear from-slate-200 to-gray-800 py-4 px-2 rounded-b-md truncate">
                            <p class="user-name"><?= htmlspecialchars($user['name']) ?></p>
                            <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="p-4 bg-white text-gray-800 container mx-auto text-center sticky bottom-0 w-full rounded shadow shadow-sky-900 hover:shadow-lg hover:shadow-sky-700">
                <strong>Total Users: </strong><?= htmlspecialchars(App\View::data('usersCount')) ?></div>
        <?php endif; ?>
    </div>

</main>